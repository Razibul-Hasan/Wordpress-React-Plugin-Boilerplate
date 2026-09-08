const gulp = require( 'gulp' );
const sass = require( 'gulp-sass' )( require( 'sass' ) );
const concat = require( 'gulp-concat' );
const cleanCSS = require( 'gulp-clean-css' );
const autoprefixer = require( 'gulp-autoprefixer' );

const fs = require( 'fs' );
const path = require( 'path' );
const zip = require( 'gulp-zip' );

const PLUGIN_SLUG = 'wp-boilerplate';

function getDateTime() {
	const d = new Date();
	return `__${ d.getFullYear() }-${ d.getMonth() + 1 }-${ d.getDate() }_${ d.getHours() }-${ d.getMinutes() }-${ d.getSeconds() }`;
}

// Compiles src/scss/frontend.scss into the single stylesheet loaded on the site.
gulp.task( 'frontend_style', function () {
	return gulp
		.src( [ './src/**/frontend.scss' ] )
		.pipe( sass().on( 'error', sass.logError ) )
		.pipe( autoprefixer( { cascade: false } ) )
		.pipe( concat( 'wpb-frontend.css' ) )
		.pipe( cleanCSS() )
		.pipe( gulp.dest( './assets/css' ) );
} );

// Concatenates every partial under src/scss/backend into the dashboard stylesheet.
gulp.task( 'backend_style', function () {
	return gulp
		.src( [ './src/**/scss/backend/**/*.scss' ] )
		.pipe( sass().on( 'error', sass.logError ) )
		.pipe( autoprefixer( { cascade: false } ) )
		.pipe( concat( 'wpb-backend.css' ) )
		.pipe( cleanCSS() )
		.pipe( gulp.dest( './assets/css' ) );
} );

gulp.task( 'watch', function () {
	gulp.watch( 'src/**/*.scss', gulp.series( 'frontend_style', 'backend_style' ) );
} );

gulp.task( 'build', gulp.series( 'frontend_style', 'backend_style' ) );

let folderName = PLUGIN_SLUG;
let destName = PLUGIN_SLUG;

// Copies the distributable files into ./build, excluding sources and tooling.
gulp.task( 'copy_files', function () {
	const filePath = path.join( __dirname, `./${ PLUGIN_SLUG }.php` );
	const date = getDateTime();
	folderName = 'Version-Unknown' + date;

	try {
		const data = fs.readFileSync( filePath, 'utf8' );
		const match = data.match( /define\( 'WPB_VER', '(\d+\.\d+\.\d+)' \);/ );
		if ( match ) {
			folderName = 'V' + match[ 1 ] + date;
			destName = destName + '-' + match[ 1 ];
		} else {
			console.log( 'Version definition not found.' );
		}
	} catch ( err ) {
		console.error( 'Error getting version: ', err );
	}

	return gulp
		.src( [
			'./**/*',
			'!./.git',
			'!./node_modules/**',
			'!./src/**',
			'!./build/**',
			'!./**/*.LICENSE.txt',
			'!.*',
			'!package.json',
			'!package-lock.json',
			'!*.js',
			'!*.md',
		] )
		.pipe(
			gulp.dest( `./build/${ folderName }/${ PLUGIN_SLUG }/`, {
				overwrite: true,
			} )
		);
} );

gulp.task( 'zip', function () {
	return gulp
		.src( `./build/${ folderName }/**` )
		.pipe( zip( destName + '.zip' ) )
		.pipe( gulp.dest( `./build/${ folderName }/` ) );
} );

gulp.task( 'package', gulp.series( 'copy_files', 'zip' ) );
