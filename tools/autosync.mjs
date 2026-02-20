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

const THROTTLE_MS = 200;
let running = false;
let lastRun = 0;

function log(...args) {
  console.log(new Date().toISOString(), "-", ...args);
}

async function autosyncNow() {
  const now = Date.now();
  if (now - lastRun < THROTTLE_MS) return;
  lastRun = now;
  if (running) return;
  running = true;

  try {
    const status = await git.status();
    if (status.conflicted.length > 0) {
      log("⚠️ Conflitti presenti: autosync fermo finché non risolvi.");
      return;
    }
    if (status.isClean()) return;

    await git.add(["-A"]);

    const status2 = await git.status();
    if (status2.isClean()) return;

    const msg = `autosave ${new Date().toLocaleString()}`;
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

log("👀 Autosync attivo: commit+push quasi immediato ad ogni save.");

chokidar
  .watch(WATCH_GLOBS, {
    ignoreInitial: true,
    awaitWriteFinish: { stabilityThreshold: 100, pollInterval: 50 },
  })
  .on("change", autosyncNow)
  .on("add", autosyncNow)
  .on("unlink", autosyncNow);
