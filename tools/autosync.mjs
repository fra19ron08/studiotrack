// tools/autosync.mjs
import chokidar from "chokidar";
import simpleGit from "simple-git";

function log(...args) {
  console.log(new Date().toISOString(), "-", ...args);
}

async function main() {
  // Trova root repo anche se avvii lo script da una sottocartella
  const gitProbe = simpleGit();
  const repoRoot = (await gitProbe.revparse(["--show-toplevel"])).trim();
  const git = simpleGit(repoRoot);

  log("Repo root:", repoRoot);
  log("Autosync attivo: create/modify/delete/rename -> add -A -> commit -> push");

  // Ignora roba che non deve MAI finire su GitHub
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

  // Queue: tanti eventi -> 1 sync solo
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

      // se sei in conflitto o rebase/merge, fermati
      if (status.conflicted?.length) {
        log("⚠️ Conflitti presenti, autosync fermo finché non risolvi.");
        return;
      }

      // Stage tutto (include anche cancellazioni)
      await git.add(["-A"]);

      const status2 = await git.status();
      if (status2.isClean()) {
        // nulla da committare
        return;
      }

      const msg = `autosave (${lastReason}) ${new Date().toLocaleString()}`;
      await git.commit(msg);
      log("✅ Commit:", msg);

      try {
        await git.push();
        log("🚀 Push OK");
      } catch (e) {
        // tipico: non-fast-forward
        log("⚠️ Push fallito, provo pull --rebase e riprovo push...");
        await git.pull(["--rebase"]);
        await git.push();
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

  // Watcher robusto per Windows: polling
  const watcher = chokidar.watch(repoRoot, {
    ignored: IGNORED,
    ignoreInitial: true,
    persistent: true,
    usePolling: true,
    interval: 250,
    awaitWriteFinish: { stabilityThreshold: 100, pollInterval: 50 },
  });

  watcher
    .on("add", (p) => { log("add", p); scheduleSync(`add:${p}`); })
    .on("change", (p) => { log("change", p); scheduleSync(`change:${p}`); })
    .on("unlink", (p) => { log("del", p); scheduleSync(`del:${p}`); })
    .on("addDir", (p) => { log("addDir", p); scheduleSync(`addDir:${p}`); })
    .on("unlinkDir", (p) => { log("delDir", p); scheduleSync(`delDir:${p}`); })
    .on("error", (e) => log("Watcher error:", String(e)));
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
