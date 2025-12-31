import { src, dest, watch, series, parallel } from "gulp";
import * as sass from 'sass';
import gulpSass from 'gulp-sass';
const sassCompiler = gulpSass(sass);

// function compileSass() {
//     return src('styles/sass/*.scss')
//         .pipe(sassCompiler({ quietDeps: true, silenceDeprecations: ["import"] }).on('error', sassCompiler.logError))
//         .pipe(dest('styles/css'));
// }

// export default function Sass() { watch("styles/**/*.scss", { delay: 500 }, compileSass); }

// function exportStyles() {
//     return src('styles/sass/index.scss')
//         .pipe(sassCompiler({ quietDeps: true, silenceDeprecations: ["import"] }).on('error', sassCompiler.logError))
//         .pipe(dest('C:/Users/annaj/Nextcloud/1_Projects/chance-theater/wp-root/wp-content/themes/chance/styles/style.css'));
// }

// export default function Sass() { watch("styles/**/*.scss", { delay: 500 }, compileSass); }

function exportMegamenu() {
    return src('./mega-menu/index.scss')
        .pipe(sassCompiler({ quietDeps: true, silenceDeprecations: ["import"] }).on('error', sassCompiler.logError))
        .pipe(dest('./mega-menu/'));
}
function watchMegamenu() { watch("./mega-menu/index.scss", { delay: 500 }, exportMegamenu); }

export default series(exportMegamenu, watchMegamenu);