const fs = require('fs');
const path = require('path');
const JSZip = require('jszip');

function addDirToZip(dirPath, zipFolder) {
  const entries = fs.readdirSync(dirPath, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dirPath, entry.name);
    if (entry.isDirectory()) {
      // Skip node_modules or .git if any
      if (entry.name === 'node_modules' || entry.name === '.git' || entry.name === 'dist') continue;
      const subFolder = zipFolder.folder(entry.name);
      addDirToZip(fullPath, subFolder);
    } else if (entry.isFile()) {
      const content = fs.readFileSync(fullPath);
      zipFolder.file(entry.name, content);
    }
  }
}

async function buildZip(sourceDir, outPath, rootFolderName) {
  const zip = new JSZip();
  const rootFolder = zip.folder(rootFolderName);
  addDirToZip(sourceDir, rootFolder);

  const content = await zip.generateAsync({
    type: 'nodebuffer',
    compression: 'DEFLATE',
    compressionOptions: { level: 9 }
  });

  fs.writeFileSync(outPath, content);
  console.log(`Successfully generated ${outPath} (${(content.length / 1024).toFixed(1)} KB)`);
}

async function main() {
  const rootDir = process.cwd();
  const downloadsDir = path.join(rootDir, 'public', 'downloads');
  if (!fs.existsSync(downloadsDir)) {
    fs.mkdirSync(downloadsDir, { recursive: true });
  }

  const themeSrc = path.join(rootDir, 'hackersshikkhok-theme');
  const coreSrc = path.join(rootDir, 'hackersshikkhok-core');

  if (fs.existsSync(themeSrc)) {
    const themeZipRoot = path.join(rootDir, 'hackersshikkhok-theme.zip');
    const themePubZip = path.join(downloadsDir, 'hackersshikkhok-theme.zip');
    const themeDirectPub = path.join(rootDir, 'public', 'hackersshikkhok-theme.zip');
    await buildZip(themeSrc, themeZipRoot, 'hackersshikkhok-theme');
    fs.copyFileSync(themeZipRoot, themePubZip);
    fs.copyFileSync(themeZipRoot, themeDirectPub);
    console.log(`Updated theme zip: ${themePubZip} and ${themeDirectPub}`);
  }

  if (fs.existsSync(coreSrc)) {
    const coreZipRoot = path.join(rootDir, 'hackersshikkhok-core.zip');
    const corePubZip = path.join(downloadsDir, 'hackersshikkhok-core.zip');
    const coreDirectPub = path.join(rootDir, 'public', 'hackersshikkhok-core.zip');
    await buildZip(coreSrc, coreZipRoot, 'hackersshikkhok-core');
    fs.copyFileSync(coreZipRoot, corePubZip);
    fs.copyFileSync(coreZipRoot, coreDirectPub);
    console.log(`Updated core zip: ${corePubZip} and ${coreDirectPub}`);
  }

  console.log('All WordPress zips successfully built with JSZip and published!');
}

main().catch((err) => {
  console.error('Error in build-zips:', err);
  process.exit(1);
});
