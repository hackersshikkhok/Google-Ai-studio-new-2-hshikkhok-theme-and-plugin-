const JSZip = require('jszip');
const fs = require('fs');
const path = require('path');

async function testZip() {
  const zip = new JSZip();
  // Simulate a plugin structure
  zip.file('hackersshikkhok-core/hackersshikkhok-core.php', '<?php // plugin file');
  zip.file('hackersshikkhok-core/includes/Core/Plugin.php', '<?php // core file');

  const content = await zip.generateAsync({ type: 'nodebuffer' });
  fs.writeFileSync('test-core.zip', content);
  console.log('Test core zip generated: test-core.zip');
}

testZip();
