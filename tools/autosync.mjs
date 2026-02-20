// tools/autosync.mjs
import chokidar from "chokidar";
import simpleGit from "simple-git";

const git = simpleGit();

const WATCH_GLOBS = [
  "**/*",
  "!**/.git/**",
  "!**/vendor/**",
  "!**/node_modules/**",
  "!**/storage/**",
  "!**/bootstrap/cache/**",
  "!**/.env",
];

// quasi immediato, evita duplicati su Windows (rename/delete spesso generano più eventi)
const THROTTLE_MS = 200;

let running = false;
let lastRun = 0;

function log(...args) {
  console.log(new Date().toISOString(), "-", ...args);
}

async function autosyncNow(reason = "fs") {
  const now = Date.now();
  if (now - lastRun < THROTTLE_MS) return;
  lastRun = now;

  if (running) return;
  running = true;

  try {
    const status = await git.status();

    // Se ci sono conflitti, non toccare nulla
    if (status.conflicted.length > 0) {
      log("⚠️ Conflitti presenti: autosync fermo finché non risolvi.");
      return;
    }

    // Se non ci sono cambi, esci
    if (status.isClean()) return;

    // -A = aggiunge, modifica e rimuove (quindi anche delete)
    await git.add(["-A"]);

    // Ricontrollo
    const status2 = await git.status();
    if (status2.isClean()) return;

    const msg = `autosave (${reason}) ${new Date().toLocaleString()}`;
    await git.commit(msg);
    log("✅ Commit:", msg);

    try {
      await git.push();
      log("🚀 Push OK");
    } catch (e) {
      log("⚠️ Push fallito. Provo pull --rebase e retry…");
      try {
        await git.pull(["--rebase"]);
        await git.push();
        log("🚀 Push OK dopo rebase");
      } catch (e2) {
        log("❌ Push ancora fallito: serve intervento manuale.");
        log(String(e2));
      }
    }
  } catch (err) {
    log("❌ Errore autosync:", String(err));
  } finally {
    running = false;
  }
}

log("👀 Autosync attivo: commit+push su create/modifica/delete/rename.");

const watcher = chokidar.watch(WATCH_GLOBS, {
  ignoreInitial: true,

  // IMPORTANTISSIMO su Windows: polling è più affidabile per add/delete/rename
  usePolling: true,
  interval: 250,

  awaitWriteFinish: { stabilityThreshold: 100, pollInterval: 50 },
});

watcher
  .on("add", (p) => autosyncNow(`add:${p}`))
  .on("change", (p) => autosyncNow(`change:${p}`))
  .on("unlink", (p) => autosyncNow(`del:${p}`))
  .on("addDir", (p) => autosyncNow(`addDir:${p}`))
  .on("unlinkDir", (p) => autosyncNow(`delDir:${p}`))
  .on("error", (err) => log("Watcher error:", String(err)));
