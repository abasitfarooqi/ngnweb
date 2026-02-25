import { PurgeCSS } from "purgecss";
import fs from "fs";
import glob from "glob";

// Pick only custom CSS, skip external/vendor
const cssFiles = glob.sync("./public/assets/css/!(*animate|*font-awesome|*bootstrap*|*icons*|*woocommerce*|*app-rtl*|*responsive*).css");
for (const file of cssFiles) {
    const original = fs.readFileSync(file, "utf-8");
  
    const purgeCSSResults = await new PurgeCSS().purge({
      content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.js",
      ],
      css: [file],
      safelist: [
        /^bg-/,
        /^text-/,
        /^btn-/,
        /^alert-/,
        /^col-/,
        /^row/,
        'active', 'show', 'fade', 'modal', 'collapse',
        /-toggle$/,
      ],
    });
  
    const purged = purgeCSSResults[0].css;
    const originalSize = (original.length / 1024).toFixed(1);
    const purgedSize = (purged.length / 1024).toFixed(1);
    const removedPercent = (100 - (purged.length / original.length) * 100).toFixed(2);
  
    console.log(`📊 ${file} Analysis Report`);
    console.log("--------------------------");
    console.log(`Original size: ${originalSize} KB`);
    console.log(`After purge:   ${purgedSize} KB`);
    console.log(`Removed:       ${removedPercent}%`);
    console.log("");
  
    const removed = purgeCSSResults[0].rejected || [];
    const kept = purgeCSSResults[0].css.match(/\.[A-Za-z0-9_-]+/g) || [];
  
    const reportDir = `./purge-report/${file.split("/").pop()}`;
    fs.mkdirSync(reportDir, { recursive: true });
  
    fs.writeFileSync(`${reportDir}/purged.css`, purged);
    fs.writeFileSync(`${reportDir}/original.css`, original);
    fs.writeFileSync(`${reportDir}/removed-selectors.txt`, removed.join("\n"));
    fs.writeFileSync(`${reportDir}/kept-selectors.txt`, [...new Set(kept)].join("\n"));
  
    console.log(`🗂 Report written to ${reportDir}/`);
  }
  