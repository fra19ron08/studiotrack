// tools/autosync.mjs
import chokidar from "chokidar";
import simpleGit from "simple-git";
import readline from "readline";

function log(...args) {
  console.log(new Date().toISOString(), "-", ...args);
}

function safeBranchName(prefix = "backup") {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, "0");
  const ts =
    d.getFullYear() +
    pad(d.getMonth() + 1) +
    pad(d.getDate()) +
    "-" +
    pad(d.getHours()) +
    pad(d.getMinutes()) +
    pad(d.getSeconds());
  return `${prefix}-${ts}`;
}

function askYesNo(question) {
  const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
  return new Promise((resolve) => {
    rl.question(question, (answer) => {
      rl.close();
      const a = String(answer || "").trim().toLowerCase();
      resolve(a === "y" || a === "yes");
    });
  });
}

async function main() {
  const gitProbe = simpleGit();
  const repoRoot = (await gitProbe.revparse(["--show-toplevel"])).trim();
  const git = simpleGit(repoRoot);

  log("Repo root:", repoRoot);

  // --- discover default remote branch (origin/HEAD -> origin/main) ---
  await git.fetch("origin");
  let remoteBranch = "main";
  try {
    const headRef = (await git.raw(["symbolic-ref", "refs/remotes/origin/HEAD"])).trim();
    remoteBranch = headRef.split("/").pop() || "main";
  } catch {}
  const remoteRef = `origin/${remoteBranch}`;
  log("Default remote branch:", remoteRef);

  // --- DOUBLE CONFIRMATION BEFORE OVERRIDE ---
  console.log("\n⚠️  ATTENZIONE: questa operazione può sovrascrivere i file locali tracciati da Git.");
  console.log("    - reset --hard su", remoteRef);
  console.log("    - clean -fd (rimuove file NON tracciati, ma NON rimuove gli ignorati tipo .env)\n");

  const c1 = await askYesNo(`Vuoi sostituire il tuo locale con la versione attuale su GitHub (${remoteRef})? (y/n): `);
  const c2 = c1
    ? await askYesNo("Conferma di nuovo: sei SICURO? Questo può eliminare modifiche locali non pushate. (y/n): ")
    : false;

  if (c1 && c2) {
    // Backup branch if local differs
    const status0 = await git.status();
    const isDirty = !status0.isClean();
    const isAhead = status0.ahead > 0;

    if (isDirty || isAhead) {
      const backup = safeBranchName("backup-local");
      log("⚠️ Local differs from remote. Creating backup branch:", backup);
      try {
        await git.raw(["branch", backup]);
      } catch (e) {
        log("⚠️ Could not create backup branch:", String(e));
      }
    }

    log("🔄 Resetting local to", remoteRef);
    await git.raw(["reset", "--hard", remoteRef]);
    await git.raw(["clean", "-fd"]);
    log("✅ Startup sync complete. Local matches", remoteRef);
  } else {
    log("ℹ️ Startup sync SKIPPED. Uso i file locali così come sono.");
  }

  // --- WATCH + AUTOCOMMIT/PUSH ---
  const IGNORED = [
    "**/.git/**",
    "**/vendor/**",
    "**/node_modules/**",
    "**/storage/**",
    "**/bootstrap/cache/**",
    "**/.env",
    "**/.DS_Store",
    // evita loop mentre modifichi lo script
    "**/tools/autosync.mjs",
  ];

  const DEBOUNCE_MS = 250;
  let timer = null;
  let running = false;
  let pending = false;
  let lastReason = "fs";

  function scheduleSync(reason) {
    lastReason = reason;
    if (timer) clearTimeout(timer);
    timer = setTimeout(runSync, DEBOUNCE_MS);
  }

  async function runSync() {
    if (running) {
      pending = true;
      return;
    }
    running = true;

    try {
      const status = await git.status();
      if (status.conflicted?.length) {
        log("⚠️ Conflitti presenti, autosync fermo finché non risolvi.");
        return;
      }

      await git.add(["-A"]); // include delete/rename

      const status2 = await git.status();
      if (status2.isClean()) return;

      const msg = `autosave (${lastReason}) ${new Date().toLocaleString()}`;
      await git.commit(msg);
      log("✅ Commit:", msg);

      try {
        await git.push("origin", remoteBranch);
        log("🚀 Push OK");
      } catch {
        log("⚠️ Push fallito. Provo pull --rebase e riprovo push...");
        await git.pull("origin", remoteBranch, ["--rebase"]);
        await git.push("origin", remoteBranch);
        log("🚀 Push OK dopo rebase");
      }
    } catch (err) {
      log("❌ Errore autosync:", String(err));
    } finally {
      running = false;
      if (pending) {
        pending = false;
        scheduleSync("pending");
      }
    }
  }

  log("👀 Autosync attivo: create/modify/delete/rename -> commit -> push");

  const watcher = chokidar.watch(repoRoot, {
    ignored: IGNORED,
    ignoreInitial: true,
    persistent: true,
    // polling più affidabile su Windows
    usePolling: true,
    interval: 250,
    awaitWriteFinish: { stabilityThreshold: 100, pollInterval: 50 },
  });

  watcher
    .on("add", (p) => scheduleSync(`add:${p}`))
    .on("change", (p) => scheduleSync(`change:${p}`))
    .on("unlink", (p) => scheduleSync(`del:${p}`))
    .on("addDir", (p) => scheduleSync(`addDir:${p}`))
    .on("unlinkDir", (p) => scheduleSync(`delDir:${p}`))
    .on("error", (e) => log("Watcher error:", String(e)));
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
