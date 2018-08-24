<?php

namespace Wpcl\Be\Modules;

use \Wpcl\Be\Classes\Utilities as Utilities;

class BECodeblock extends \FLBuilderModule implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber, \Wpcl\Be\Interfaces\Filter_Hook_Subscriber {

	/**
	 * API Manager / Loader to interact with the other parts of the plugin
	 * @since 1.0.0
	 * @var (object) $api : The instance of the api manager class
	 */
	protected $api;

	/**
	 * Hook Name
	 * @since 1.0.0
	 * @var [string] : hook name, same as the slug created later by FLBuilderModule
	 */
	protected $hook_name;

	/**
	 * @method __construct
	 */
	public function __construct() {

		/**
		 * Set the hook name. Same as the slug, but created here so we can access it
		 */
		$this->hook_name = basename( __FILE__, '.php' );

		/**
		 * Get the API instance to interact with the other parts of our plugin
		 */
		$this->api = \Wpcl\Be\Loader::get_instance( $this );

		/**
		 * Construct our parent class (FLBuilderModule);
		 */
		parent::__construct( array(
			'name'          	=> __( 'Code Block', 'wpcl_beaver_extender' ),
			'description'   	=> __( 'Display a block of code', 'wpcl_beaver_extender' ),
			'category'      	=> __( 'Beaver Extender', 'wpcl_beaver_extender' ),
			'partial_refresh'	=> true,
		));

		/**
		 * Enqueue additional JavaScript
		 * Registered in includes/classes/frontend.php
		 */
		$this->add_js( 'prism-js' );
		$this->add_css( 'prism' );
	}

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( "beaver_extender_frontend_{$this->hook_name}" => array( 'do_frontend' , 10, 3 ) ),
			array( "beaver_extender_css_{$this->hook_name}" => array( 'do_css' , 10, 3 ) ),
			array( "beaver_extender_js_{$this->hook_name}" => array( 'do_js' , 10, 3 ) ),
		);
	}

	/**
	 * Get the filter hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {
		return array(

		);
	}

	/**
	 * Organize the front end output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_frontend( $module, $settings, $id ) {
		// Bail if it's not this specific instance
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}
	}

	/**
	 * Organize the css output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_css( $module, $settings, $id ) {
		/**
		 * Bail if not this instance
		 */
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}
	}

	/**
	 * Organize the js output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_js( $module, $settings, $id ) {
		/**
		 * Bail if not this instance
		 */
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}
	}

	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		\FLBuilder::register_module( __CLASS__ , array(
			'general'       => array(
				'title'         => __( 'General', 'wpcl_beaver_extender' ),
				'sections'      => array(
					'general'       => array(
						'title'         => '',
						'fields'        => array(
							'code'          => array(
								'type'          => 'textarea',
								'rows'          => '14',
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'language'          => array(
								'type'          => 'select',
								'label'         => __( 'Language', 'wpcl_beaver_extender' ),
								'default'       => '',
								'preview'       => array(
									'type'          => 'refresh',
								),
								'options'       => array(
									''          => __( 'None', 'wpcl_beaver_extender' ),
									'markup' => __( 'Markup', 'wpcl_beaver_extender' ),
									'css' => __( 'CSS', 'wpcl_beaver_extender' ),
									'clike' => __( 'C-like', 'wpcl_beaver_extender' ),
									'javascript' => __( 'JavaScript', 'wpcl_beaver_extender' ),
									'abap' => __( 'ABAP', 'wpcl_beaver_extender' ),
									'actionscript' => __( 'ActionScript', 'wpcl_beaver_extender' ),
									'ada' => __( 'Ada', 'wpcl_beaver_extender' ),
									'apacheconf' => __( 'Apache Configuration', 'wpcl_beaver_extender' ),
									'apl' => __( 'APL', 'wpcl_beaver_extender' ),
									'applescript' => __( 'AppleScript', 'wpcl_beaver_extender' ),
									'arduino' => __( 'Arduino', 'wpcl_beaver_extender' ),
									'arff' => __( 'ARFF', 'wpcl_beaver_extender' ),
									'asciidoc' => __( 'AsciiDoc', 'wpcl_beaver_extender' ),
									'asm6502' => __( '6502 Assembly', 'wpcl_beaver_extender' ),
									'aspnet' => __( 'ASP.NET (C#)', 'wpcl_beaver_extender' ),
									'autohotkey' => __( 'AutoHotkey', 'wpcl_beaver_extender' ),
									'autoit' => __( 'AutoIt', 'wpcl_beaver_extender' ),
									'bash' => __( 'Bash', 'wpcl_beaver_extender' ),
									'basic' => __( 'BASIC', 'wpcl_beaver_extender' ),
									'batch' => __( 'Batch', 'wpcl_beaver_extender' ),
									'bison' => __( 'Bison', 'wpcl_beaver_extender' ),
									'brainfuck' => __( 'Brainfuck', 'wpcl_beaver_extender' ),
									'bro' => __( 'Bro', 'wpcl_beaver_extender' ),
									'c' => __( 'C', 'wpcl_beaver_extender' ),
									'csharp' => __( 'C#', 'wpcl_beaver_extender' ),
									'cpp' => __( 'C++', 'wpcl_beaver_extender' ),
									'coffeescript' => __( 'CoffeeScript', 'wpcl_beaver_extender' ),
									'clojure' => __( 'Clojure', 'wpcl_beaver_extender' ),
									'crystal' => __( 'Crystal', 'wpcl_beaver_extender' ),
									'csp' => __( 'Content-Security-Policy', 'wpcl_beaver_extender' ),
									'css-extras' => __( 'CSS Extras', 'wpcl_beaver_extender' ),
									'd' => __( 'D', 'wpcl_beaver_extender' ),
									'dart' => __( 'Dart', 'wpcl_beaver_extender' ),
									'diff' => __( 'Diff', 'wpcl_beaver_extender' ),
									'django' => __( 'Django/Jinja2', 'wpcl_beaver_extender' ),
									'docker' => __( 'Docker', 'wpcl_beaver_extender' ),
									'eiffel' => __( 'Eiffel', 'wpcl_beaver_extender' ),
									'elixir' => __( 'Elixir', 'wpcl_beaver_extender' ),
									'elm' => __( 'Elm', 'wpcl_beaver_extender' ),
									'erb' => __( 'ERB', 'wpcl_beaver_extender' ),
									'erlang' => __( 'Erlang', 'wpcl_beaver_extender' ),
									'fsharp' => __( 'F#', 'wpcl_beaver_extender' ),
									'flow' => __( 'Flow', 'wpcl_beaver_extender' ),
									'fortran' => __( 'Fortran', 'wpcl_beaver_extender' ),
									'gedcom' => __( 'GEDCOM', 'wpcl_beaver_extender' ),
									'gherkin' => __( 'Gherkin', 'wpcl_beaver_extender' ),
									'git' => __( 'Git', 'wpcl_beaver_extender' ),
									'glsl' => __( 'GLSL', 'wpcl_beaver_extender' ),
									'go' => __( 'Go', 'wpcl_beaver_extender' ),
									'graphql' => __( 'GraphQL', 'wpcl_beaver_extender' ),
									'groovy' => __( 'Groovy', 'wpcl_beaver_extender' ),
									'haml' => __( 'Haml', 'wpcl_beaver_extender' ),
									'handlebars' => __( 'Handlebars', 'wpcl_beaver_extender' ),
									'haskell' => __( 'Haskell', 'wpcl_beaver_extender' ),
									'haxe' => __( 'Haxe', 'wpcl_beaver_extender' ),
									'http' => __( 'HTTP', 'wpcl_beaver_extender' ),
									'hpkp' => __( 'HTTP Public-Key-Pins', 'wpcl_beaver_extender' ),
									'hsts' => __( 'HTTP Strict-Transport-Security', 'wpcl_beaver_extender' ),
									'ichigojam' => __( 'IchigoJam', 'wpcl_beaver_extender' ),
									'icon' => __( 'Icon', 'wpcl_beaver_extender' ),
									'inform7' => __( 'Inform 7', 'wpcl_beaver_extender' ),
									'ini' => __( 'Ini', 'wpcl_beaver_extender' ),
									'io' => __( 'Io', 'wpcl_beaver_extender' ),
									'j' => __( 'J', 'wpcl_beaver_extender' ),
									'java' => __( 'Java', 'wpcl_beaver_extender' ),
									'jolie' => __( 'Jolie', 'wpcl_beaver_extender' ),
									'json' => __( 'JSON', 'wpcl_beaver_extender' ),
									'julia' => __( 'Julia', 'wpcl_beaver_extender' ),
									'keyman' => __( 'Keyman', 'wpcl_beaver_extender' ),
									'kotlin' => __( 'Kotlin', 'wpcl_beaver_extender' ),
									'latex' => __( 'LaTeX', 'wpcl_beaver_extender' ),
									'less' => __( 'Less', 'wpcl_beaver_extender' ),
									'liquid' => __( 'Liquid', 'wpcl_beaver_extender' ),
									'lisp' => __( 'Lisp', 'wpcl_beaver_extender' ),
									'livescript' => __( 'LiveScript', 'wpcl_beaver_extender' ),
									'lolcode' => __( 'LOLCODE', 'wpcl_beaver_extender' ),
									'lua' => __( 'Lua', 'wpcl_beaver_extender' ),
									'makefile' => __( 'Makefile', 'wpcl_beaver_extender' ),
									'markdown' => __( 'Markdown', 'wpcl_beaver_extender' ),
									'markup-templating' => __( 'Markup templating', 'wpcl_beaver_extender' ),
									'matlab' => __( 'MATLAB', 'wpcl_beaver_extender' ),
									'mel' => __( 'MEL', 'wpcl_beaver_extender' ),
									'mizar' => __( 'Mizar', 'wpcl_beaver_extender' ),
									'monkey' => __( 'Monkey', 'wpcl_beaver_extender' ),
									'n4js' => __( 'N4JS', 'wpcl_beaver_extender' ),
									'nasm' => __( 'NASM', 'wpcl_beaver_extender' ),
									'nginx' => __( 'nginx', 'wpcl_beaver_extender' ),
									'nim' => __( 'Nim', 'wpcl_beaver_extender' ),
									'nix' => __( 'Nix', 'wpcl_beaver_extender' ),
									'nsis' => __( 'NSIS', 'wpcl_beaver_extender' ),
									'objectivec' => __( 'Objective-C', 'wpcl_beaver_extender' ),
									'ocaml' => __( 'OCaml', 'wpcl_beaver_extender' ),
									'opencl' => __( 'OpenCL', 'wpcl_beaver_extender' ),
									'oz' => __( 'Oz', 'wpcl_beaver_extender' ),
									'parigp' => __( 'PARI/GP', 'wpcl_beaver_extender' ),
									'parser' => __( 'Parser', 'wpcl_beaver_extender' ),
									'pascal' => __( 'Pascal', 'wpcl_beaver_extender' ),
									'perl' => __( 'Perl', 'wpcl_beaver_extender' ),
									'php' => __( 'PHP', 'wpcl_beaver_extender' ),
									'php-extras' => __( 'PHP Extras', 'wpcl_beaver_extender' ),
									'plsql' => __( 'PL/SQL', 'wpcl_beaver_extender' ),
									'powershell' => __( 'PowerShell', 'wpcl_beaver_extender' ),
									'processing' => __( 'Processing', 'wpcl_beaver_extender' ),
									'prolog' => __( 'Prolog', 'wpcl_beaver_extender' ),
									'properties' => __( '.properties', 'wpcl_beaver_extender' ),
									'protobuf' => __( 'Protocol Buffers', 'wpcl_beaver_extender' ),
									'pug' => __( 'Pug', 'wpcl_beaver_extender' ),
									'puppet' => __( 'Puppet', 'wpcl_beaver_extender' ),
									'pure' => __( 'Pure', 'wpcl_beaver_extender' ),
									'python' => __( 'Python', 'wpcl_beaver_extender' ),
									'q' => __( 'Q (kdb+ database)', 'wpcl_beaver_extender' ),
									'qore' => __( 'Qore', 'wpcl_beaver_extender' ),
									'r' => __( 'R', 'wpcl_beaver_extender' ),
									'jsx' => __( 'React JSX', 'wpcl_beaver_extender' ),
									'tsx' => __( 'React TSX', 'wpcl_beaver_extender' ),
									'renpy' => __( 'Renpy', 'wpcl_beaver_extender' ),
									'reason' => __( 'Reason', 'wpcl_beaver_extender' ),
									'rest' => __( 'reST (reStructuredText)', 'wpcl_beaver_extender' ),
									'rip' => __( 'Rip', 'wpcl_beaver_extender' ),
									'roboconf' => __( 'Roboconf', 'wpcl_beaver_extender' ),
									'ruby' => __( 'Ruby', 'wpcl_beaver_extender' ),
									'rust' => __( 'Rust', 'wpcl_beaver_extender' ),
									'sas' => __( 'SAS', 'wpcl_beaver_extender' ),
									'sass' => __( 'Sass (Sass)', 'wpcl_beaver_extender' ),
									'scss' => __( 'Sass (Scss)', 'wpcl_beaver_extender' ),
									'scala' => __( 'Scala', 'wpcl_beaver_extender' ),
									'scheme' => __( 'Scheme', 'wpcl_beaver_extender' ),
									'smalltalk' => __( 'Smalltalk', 'wpcl_beaver_extender' ),
									'smarty' => __( 'Smarty', 'wpcl_beaver_extender' ),
									'sql' => __( 'SQL', 'wpcl_beaver_extender' ),
									'soy' => __( 'Soy (Closure Template)', 'wpcl_beaver_extender' ),
									'stylus' => __( 'Stylus', 'wpcl_beaver_extender' ),
									'swift' => __( 'Swift', 'wpcl_beaver_extender' ),
									'tap' => __( 'TAP', 'wpcl_beaver_extender' ),
									'tcl' => __( 'Tcl', 'wpcl_beaver_extender' ),
									'textile' => __( 'Textile', 'wpcl_beaver_extender' ),
									'tt2' => __( 'Template Toolkit 2', 'wpcl_beaver_extender' ),
									'twig' => __( 'Twig', 'wpcl_beaver_extender' ),
									'typescript' => __( 'TypeScript', 'wpcl_beaver_extender' ),
									'vbnet' => __( 'VB.Net', 'wpcl_beaver_extender' ),
									'velocity' => __( 'Velocity', 'wpcl_beaver_extender' ),
									'verilog' => __( 'Verilog', 'wpcl_beaver_extender' ),
									'vhdl' => __( 'VHDL', 'wpcl_beaver_extender' ),
									'vim' => __( 'vim', 'wpcl_beaver_extender' ),
									'visual-basic' => __( 'Visual Basic', 'wpcl_beaver_extender' ),
									'wasm' => __( 'WebAssembly', 'wpcl_beaver_extender' ),
									'wiki' => __( 'Wiki markup', 'wpcl_beaver_extender' ),
									'xeora' => __( 'Xeora', 'wpcl_beaver_extender' ),
									'xojo' => __( 'Xojo (REALbasic)', 'wpcl_beaver_extender' ),
									'xquery' => __( 'XQuery', 'wpcl_beaver_extender' ),
									'yaml' => __( 'YAML', 'wpcl_beaver_extender' ),
								),
							),
						),
					),
				),
			),
		));
	}
}