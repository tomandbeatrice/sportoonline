const fs = require('fs');
const path = require('path');

// Scan directories recursively, skip vendor, node_modules, .git, public/emails/icons (svg files are fine but not needed to scan)
const SKIP_DIRS = new Set(['node_modules', '.git', 'vendor', 'public\\emails\\icons']);
const root = path.resolve(__dirname, '..');

// Unicode property escape for Emoji (Node >=10 with --harmony or modern Node). Fallback regex included.
let emojiRegex;
try {
  emojiRegex = new RegExp('\\p{Emoji}', 'u');
} catch (e) {
  // fallback conservative range (covers many common emojis)
  emojiRegex = /[\u231A-\u231B\u23E9-\u23EC\u23F0\u23F3\u25FD-\u25FE\u2614-\u2615\u2648-\u2653\u267F\u2693\u26A1\u26AA-\u26AB\u26BD-\u26BE\u26C4-\u26C5\u2705\u270A-\u270B\u2728\u274C\u274E\u2753-\u2755\u2795-\u2797\u27B0\u27BF\u2B50\u2B55\u1F300-\u1F6FF\u1F900-\u1F9FF]/u;
}

const out = [];

function walk(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    if (entry.isDirectory()) {
      if (SKIP_DIRS.has(entry.name)) continue;
      walk(path.join(dir, entry.name));
    } else if (entry.isFile()) {
      const rel = path.relative(root, path.join(dir, entry.name));
      // Only scan text files by extension
      const ext = path.extname(entry.name).toLowerCase();
      const textExts = ['.php','.vue','.js','.ts','.jsx','.tsx','.json','.md','.html','.blade.php','.css','.scss','.less','.txt'];
      // allow files without extension too
      let shouldScan = textExts.includes(ext) || ext === '';
      // skip binary-ish files
      if (!shouldScan) continue;
      let content;
      try {
        content = fs.readFileSync(path.join(dir, entry.name), 'utf8');
      } catch (err) {
        continue;
      }
      const lines = content.split(/\r?\n/);
      for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (emojiRegex.test(line)) {
          // capture all emoji characters found
          let matches = [];
          try {
            // global match using unicode property if supported
            const g = new RegExp('\\p{Emoji}', 'gu');
            matches = line.match(g) || [];
          } catch (e) {
            // fallback to our regex
            const g = new RegExp(emojiRegex.source, 'gu');
            matches = line.match(g) || [];
          }
          out.push({ file: rel.replace(/\\/g, '/'), line: i+1, snippet: line.trim(), matches });
        }
      }
    }
  }
}

walk(root);

const outPath = path.join(root, 'emoji-report-full.json');
fs.writeFileSync(outPath, JSON.stringify({ generated_at: new Date().toISOString(), count: out.length, entries: out }, null, 2), 'utf8');
console.log('Wrote', outPath, 'entries:', out.length);
