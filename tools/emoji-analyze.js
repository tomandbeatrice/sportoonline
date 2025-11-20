const fs = require('fs');
const path = require('path');
const root = path.resolve(__dirname, '..');
const reportPath = path.join(root, 'emoji-report-full.json');
if (!fs.existsSync(reportPath)) {
  console.error('emoji-report-full.json not found at', reportPath);
  process.exit(1);
}
let dataRaw = fs.readFileSync(reportPath, 'utf8');
let data;
try {
  data = JSON.parse(dataRaw);
} catch (e) {
  console.error('Failed to parse JSON:', e.message);
  process.exit(1);
}
// data may be array or object
let entries = [];
if (Array.isArray(data)) entries = data;
else if (data.entries) entries = data.entries;
else if (data.files) entries = data.files.flatMap(f => f.matches.map(m=>({file:f.path,line:m.line_snippet?m.line:undefined,snippet:m.line_snippet, matches:m.emojis||m.matches||[]})));

const perFile = new Map();
const emojiCounts = new Map();
for (const e of entries) {
  const file = e.file || e.path || 'unknown';
  const arr = e.matches || e.emojis || [];
  const count = arr.length || (arr.length===0 && (e.snippet && (e.snippet.match(/\p{Emoji}/gu)||[]).length)) || 0;
  // ensure per-file entry
  if (!perFile.has(file)) perFile.set(file,{count:0, samples:[]});
  const rec = perFile.get(file);
  rec.count += count;
  if (rec.samples.length < 5) rec.samples.push({line:e.line, snippet:e.snippet, matches:arr});
  // emoji frequency
  for (const ch of arr) {
    emojiCounts.set(ch, (emojiCounts.get(ch)||0) + 1);
  }
  // fallback: if arr empty, try extract from snippet
  if ((arr==null || arr.length===0) && e.snippet) {
    try {
      const g = e.snippet.match(/\p{Emoji}/gu) || [];
      for (const ch of g) emojiCounts.set(ch, (emojiCounts.get(ch)||0)+1);
      perFile.get(file).count += g.length;
    } catch (err) {
      const g = e.snippet.match(/[\u231A-\u231B\u23E9-\u23EC\u23F0\u23F3\u25FD-\u25FE\u2614-\u2615\u2648-\u2653\u267F\u2693\u26A1\u26AA-\u26AB\u26BD-\u26BE\u26C4-\u26C5\u2705\u270A-\u270B\u2728\u274C\u274E\u2753-\u2755\u2795-\u2797\u27B0\u27BF\u2B50\u2B55\u1F300-\u1F6FF\u1F900-\u1F9FF]/gu) || [];
      for (const ch of g) emojiCounts.set(ch, (emojiCounts.get(ch)||0)+1);
      perFile.get(file).count += g.length;
    }
  }
}

// top files
const topFiles = Array.from(perFile.entries()).map(([file,v])=>({file,count:v.count,samples:v.samples})).sort((a,b)=>b.count-a.count).slice(0,20);
const topEmojis = Array.from(emojiCounts.entries()).map(([e,c])=>({emoji:e,count:c})).sort((a,b)=>b.count-b.count).slice(0,30);

const out = {generated_at:new Date().toISOString(), total_entries:entries.length, unique_files: perFile.size, top_files: topFiles, top_emojis: topEmojis};
const outPath = path.join(root,'emoji-report-summary.json');
fs.writeFileSync(outPath, JSON.stringify(out,null,2),'utf8');
console.log('Wrote', outPath);
