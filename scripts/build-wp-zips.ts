import fs from 'fs';
import path from 'path';
import { ZipArchive } from 'archiver';

async function zipDirectory(sourceDir: string, outPath: string, rootFolder: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const output = fs.createWriteStream(outPath);
    const archive = new ZipArchive({ zlib: { level: 9 } });

    output.on('close', () => {
      console.log(`Successfully zipped ${sourceDir} -> ${outPath} (${archive.pointer()} total bytes)`);
      resolve();
    });

    archive.on('error', (err: any) => {
      reject(err);
    });

    archive.pipe(output);
    archive.directory(sourceDir, rootFolder);
    archive.finalize();
  });
}

async function main() {
  const rootDir = process.cwd();
  
  // Ensure public/downloads exists
  const downloadsDir = path.join(rootDir, 'public', 'downloads');
  if (!fs.existsSync(downloadsDir)) {
    fs.mkdirSync(downloadsDir, { recursive: true });
  }

  const themeSrc = path.join(rootDir, 'hackersshikkhok-theme');
  const coreSrc = path.join(rootDir, 'hackersshikkhok-core');

  if (fs.existsSync(themeSrc)) {
    const themeZipRoot = path.join(rootDir, 'hackersshikkhok-theme.zip');
    const themePubZip = path.join(downloadsDir, 'hackersshikkhok-theme.zip');
    await zipDirectory(themeSrc, themeZipRoot, 'hackersshikkhok-theme');
    fs.copyFileSync(themeZipRoot, themePubZip);
    console.log(`Copied theme zip to ${themePubZip}`);
  }

  if (fs.existsSync(coreSrc)) {
    const coreZipRoot = path.join(rootDir, 'hackersshikkhok-core.zip');
    const corePubZip = path.join(downloadsDir, 'hackersshikkhok-core.zip');
    await zipDirectory(coreSrc, coreZipRoot, 'hackersshikkhok-core');
    fs.copyFileSync(coreZipRoot, corePubZip);
    console.log(`Copied core zip to ${corePubZip}`);
  }

  console.log('All WordPress packages successfully re-zipped and published to public/downloads/!');
}

main().catch((err) => {
  console.error('Error building WordPress zips:', err);
  process.exit(1);
});
