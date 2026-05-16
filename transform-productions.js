import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const filePath = path.join(__dirname, './src/scss/past-productions.html');
let content = fs.readFileSync(filePath, 'utf8');

// Helper function: convert ALL CAPS to Capitalize Case
function toCapitalizeCase(str) {
  return str
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ');
}

// Helper function: convert comma/& separated list to list items
function creditsToList(creditsText) {
  // Handle "and" and commas as separators
  const items = creditsText
    .split(/\s*,\s*/)
    .map(item => item.trim())
    .filter(item => item.length > 0)
    .flatMap(item => item.split(/\s+and\s+/))
    .map(item => item.trim())
    .filter(item => item.length > 0);

  return items
    .map(item => `<li>${item}</li>`)
    .join('\n');
}

// 1. Convert series paragraphs to h3
// Match patterns like <p>Main Series</p>, <p>TYA Family Series</p>, etc.
content = content.replace(
  /<p>(Main Series|TYA Family Series|OTR Reading Series|Holiday Series)<\/p>/g,
  '<!-- wp:heading {"level":3} -->\n<h3 class="wp-block-heading">$1</h3>\n<!-- /wp:heading -->'
);

// 2. Convert production titles (all caps links) to h4 with Capitalize Case
// This regex finds <a>ALL CAPS TEXT</a> patterns
content = content.replace(
  /<a href="([^"]*)"[^>]*>([A-Z\s:,&'()!.\-–—]+)<\/a>/g,
  function (match, href, title) {
    // Check if it's all caps (convert if all letters are caps or spaces/punctuation)
    if (/^[A-Z\s:,&'()!.\-–—]+$/.test(title)) {
      const capitalizedTitle = toCapitalizeCase(title);
      return `<!-- wp:heading {"level":4} -->\n<h4 class="wp-block-heading"><a href="${href}">${capitalizedTitle}</a></h4>\n<!-- /wp:heading -->`;
    }
    return match;
  }
);

// 3. Convert credits to lists
// Match patterns like "-- by Author1, Author2, Author3" or "-- book & lyrics by X, music by Y"
content = content.replace(
  /-- ([^<\n]+(?:by|composed by|adapted by|written by)[^<\n]+)(?=<br|<\/p)/gi,
  function (match, credits) {
    const creditList = creditsToList(credits);
    return `<!-- wp:list -->\n<ul>\n${creditList}\n</ul>\n<!-- /wp:list -->`;
  }
);

// 4. Convert awards (lines starting with *) to lists
// Match <br>\n* Award text
content = content.replace(
  /<br>\n\*\s+([^\n<]+(?:\n\*\s+[^\n<]+)*)/g,
  function (match, awards) {
    const awardLines = awards
      .split(/\n\*\s+/)
      .map(line => line.trim())
      .filter(line => line.length > 0);

    const listItems = awardLines
      .map(award => `<li>${award}</li>`)
      .join('\n');

    return `<!-- wp:list -->\n<ul>\n${listItems}\n</ul>\n<!-- /wp:list -->`;
  }
);

// 5. Remove separator lines
content = content.replace(
  /<!-- wp:separator -->\s*<hr[^>]*\/?\s*>\s*<!-- \/wp:separator -->\s*/g,
  ''
);

// Write the modified content back
fs.writeFileSync(filePath, content, 'utf8');
console.log('Transformation complete!');
