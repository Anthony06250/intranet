'use strict';

const { series, src, dest, parallel, watch } = require("gulp"),
    autoprefixer = require("gulp-autoprefixer"),
    babel = require('gulp-babel'),
    browsersync = require("browser-sync"),
    concat = require("gulp-concat"),
    CleanCSS = require("gulp-clean-css"),
    del = require("del"),
    imagemin = require("gulp-imagemin"),
    inquirer = require("inquirer"),
    npmdist = require("gulp-npm-dist"),
    newer = require("gulp-newer"),
    rename = require("gulp-rename"),
    rtlcss = require("gulp-rtlcss"),
    sourcemaps = require("gulp-sourcemaps"),
    sass = require("gulp-sass")(require("sass")),
    uglify = require("gulp-uglify");

/**
 * @type {{baseSrcAssets: string, baseDist: string, baseDistAssets: string, baseSrc: string}}
 */
const paths = {
    baseSrc: "./assets/",               // source directory
    baseDist: "./public/assets/",       // build directory
    baseDistAssets: "./public/assets/", // build assets directory
    baseSrcAssets: "./assets/"          // source assets directory
};

/**
 * @type {{Creative: string, Saas: string, Modern: string}}
 */
const demoPaths = {
    Saas: "saas",
    Modern: "modern",
    Creative: "creative",
};

let demo = "";
let demoPath = "";

/**
 * @param done
 * @returns {Promise<void>}
 */
const input = async function (done) {
    let message =
        "--------------------------------------------------------------\n";
    message += "Hyper - v5.1.0\n";
    message += "Which demo version would you like to run?\n";
    message +=
        "----------------------------------------------------------------\n";
    const res = await inquirer.prompt({
        type: "list",
        name: "demo",
        message,
        default: "Saas",
        choices: [
            "Saas",
            "Modern",
            "Creative",
        ],
        pageSize: "7",
    });
    demo = res.demo;
    demoPath = demoPaths[demo];
    done();
};

/**
 * @param done
 */
const clean = function (done) {
    del.sync(paths.baseDist, done());
};

/**
 * @returns {*}
 */
const vendor = function () {
    const out = paths.baseDistAssets + "vendor/";
    return src(npmdist(), { base: "./node_modules" })
        .pipe(rename(function (path) {
            path.dirname = path.dirname.replace(/\/dist/, '').replace(/\\dist/, '');
        }))
        .pipe(dest(out));
};

/**
 * @returns {*}
 */
const data = function () {
    const out = paths.baseDistAssets + "data/";
    return src([paths.baseSrcAssets + "data/**/*"])
        .pipe(dest(out));
};

/**
 * @returns {*}
 */
const fonts = function () {
    const out = paths.baseDistAssets + "fonts/";
    return src([paths.baseSrcAssets + "fonts/**/*"])
        .pipe(newer(out))
        .pipe(dest(out));
};

/**
 * @returns {*}
 */
const images = function () {
    var out = paths.baseDistAssets + "images";
    return src(paths.baseSrcAssets + "images/**/*")
        .pipe(newer(out))
        .pipe(imagemin())
        .pipe(dest(out));
};

/**
 * @returns {*}
 */
const javascript = function () {
    const out = paths.baseDistAssets + "js/";

    // vendor.min.js
    src([
        paths.baseDistAssets + "vendor/jquery/jquery.min.js",
        paths.baseDistAssets + "vendor/bootstrap/js/bootstrap.bundle.min.js",
        paths.baseDistAssets + "vendor/simplebar/simplebar.min.js"
    ])
        .pipe(concat("vendor.js"))
        .pipe(rename({ suffix: ".min" }))
        .pipe(dest(out));

    // copying and minifying all other js
    src([paths.baseSrcAssets + "js/**/*.js", "!" + paths.baseSrcAssets + "js/hyper-layout.js", "!" + paths.baseSrcAssets + "js/hyper-main.js"])
        .pipe(uglify())
        // .pipe(rename({ suffix: ".min" }))
        .pipe(dest(out));

    // copying all json
    src([paths.baseSrcAssets + "js/**/*.json"])
        // .pipe(uglify())
        // .pipe(rename({ suffix: ".min" }))
        .pipe(dest(out));

    // app.js (hyper-main.js + hyper-layout.js)
    return src([paths.baseSrcAssets + "js/hyper-main.js", paths.baseSrcAssets + "js/hyper-layout.js"])
        .pipe(concat("app.js"))
        .pipe(dest(out))
        .pipe(babel({
            presets: ['@babel/env']
        }))
        .pipe(uglify())
        .pipe(rename({ suffix: ".min" }))
        .pipe(dest(out));
};

/**
 * @returns {*}
 */
const scss = function () {
    const out = paths.baseDistAssets + "css/";

    src(paths.baseSrcAssets + `scss/app-${demoPath}.scss`)
        .pipe(sourcemaps.init())
        .pipe(sass.sync()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"],
            })
        )
        .pipe(dest(out))
        .pipe(CleanCSS())
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("./")) // source maps
        .pipe(dest(out));

    src(paths.baseSrcAssets + `scss/app-${demoPath}-pdf.scss`)
        .pipe(sourcemaps.init())
        .pipe(sass.sync()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"],
            })
        )
        .pipe(dest(out))
        .pipe(CleanCSS())
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("./")) // source maps
        .pipe(dest(out));

    // generate rtl
    return src(paths.baseSrcAssets + `scss/app-${demoPath}.scss`)
        .pipe(sourcemaps.init())
        .pipe(sass.sync()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"],
            })
        )
        .pipe(rtlcss())
        .pipe(rename({ suffix: "-rtl" }))
        .pipe(dest(out))
        .pipe(CleanCSS())
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("./")) // source maps
        .pipe(dest(out));
};

/**
 * @returns {*}
 */
const icons = function () {
    const out = paths.baseDistAssets + "css/";
    return src(paths.baseSrcAssets + "scss/icons.scss")
        .pipe(sourcemaps.init())
        .pipe(sass.sync()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"],
            })
        )
        .pipe(dest(out))
        .pipe(CleanCSS())
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("./")) // source maps
        .pipe(dest(out));
};

function watchFiles() {
    watch(paths.baseSrcAssets + "data/**/*", series(data));
    watch(paths.baseSrcAssets + "fonts/**/*", series(fonts));
    watch(paths.baseSrcAssets + "images/**/*", series(images));
    watch(paths.baseSrcAssets + "js/**/*", series(javascript));
    watch(paths.baseSrcAssets + "scss/icons.scss", series(icons));
    watch([paths.baseSrcAssets + "scss/**/*.scss", "!" + paths.baseSrcAssets + "scss/icons.scss"], series(scss));
}

// Production Tasks
exports.default = series(
    input,
    clean,
    vendor,
    parallel(data, fonts, images, javascript, scss, icons),
    parallel(watchFiles)
);

// Build Tasks
exports.build = series(
    input,
    clean,
    vendor,
    parallel(data, fonts, images, javascript, scss, icons)
);

// Watch Tasks
exports.watch = series(
    parallel(watchFiles)
);

// Docs
exports.docs = function () {
    browsersync.init({
        server: {
            baseDir: "docs",
        },
    });
};
