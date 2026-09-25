/**
 * زرین‌کوچ — رفتارهای پنل تنظیمات:
 * جستجوی زنده، عنوان بخش جاری، میان‌بُرهای پیشخوان، کارت‌های پالت و شمارنده‌ی نویسه.
 */
( function ( $ ) {
	'use strict';

	var cfg = window.zcPanel || {};
	var $root, $menu, $search, index = null, timer = null;

	/** یکسان‌سازی متن فارسی برای جستجو. */
	function norm( str ) {
		return String( str || '' )
			.toLowerCase()
			.replace( /[\u064A\u0649]/g, '\u06CC' ) // ي ى → ی
			.replace( /\u0643/g, '\u06A9' ) // ك → ک
			.replace( /[\u064B-\u065F\u0670]/g, '' ) // اعراب
			.replace( /[\u200c\u200f\u200e]/g, '' ) // نیم‌فاصله و نشانه‌های جهت
			.replace( /[*:،,؛.()«»"']/g, ' ' )
			.replace( /\s+/g, ' ' )
			.trim();
	}

	function hasField( tr ) {
		var fs = tr.querySelector( ':scope > td > fieldset.redux-field' );
		// فیلد info محتوایش را بیرون از جدول چاپ می‌کند و ردیفی خالی به جا می‌گذارد.
		return !! fs && ! ( fs.classList.contains( 'redux-container-info' ) && ! fs.children.length );
	}

	/** ردیف‌های بی‌فیلد (اسکریپت‌های Redux) فضای خالی نسازند. */
	function markEmptyRows() {
		$root.find( 'table.form-table > tbody > tr' ).each( function () {
			if ( ! hasField( this ) ) {
				this.classList.add( 'zc-empty-row' );
			}
		} );
	}

	/* ------------------------------------------------------------ */
	/* عنوان بخش جاری در نوار چسبان                                    */
	/* ------------------------------------------------------------ */
	function updateCurrent() {
		var $li = $menu.find( 'li.redux-group-tab-link-li.active' ).last();
		var rel = $li.children( 'a' ).attr( 'data-rel' );
		if ( rel ) {
			$root.find( '.redux-group-tab' ).each( function () {
				var on = this.getAttribute( 'data-rel' ) === String( rel );
				this.classList.toggle( 'zc-tab-active', on );
				if ( ! on && this.style.display === 'block' ) {
					this.style.display = '';
				}
			} );
		}
		var label = $.trim( $li.find( '> a .group_title' ).first().text() );
		var $parent = $li.parents( 'li.redux-group-tab-link-li' ).first();
		if ( $parent.length ) {
			label = $.trim( $parent.find( '> a .group_title' ).first().text() ) + ' / ' + label;
		}
		if ( label ) {
			$( '#zc-current-section' ).text( label );
		}
	}

	/* ------------------------------------------------------------ */
	/* جستجو                                                        */
	/* ------------------------------------------------------------ */
	function buildIndex() {
		index = [];
		$root.find( '.redux-group-tab' ).each( function () {
			var tab = this;
			var rel = tab.getAttribute( 'data-rel' );
			var entry = { tab: tab, rel: rel, rows: [], title: norm( $( tab ).children( 'h2' ).text() ) };
			var group = '';
			tab.querySelectorAll( '.redux-section-field h3, table.form-table > tbody > tr' ).forEach( function ( el ) {
				if ( 'H3' === el.tagName ) {
					group = norm( el.textContent );
					return;
				}
				if ( ! hasField( el ) ) {
					entry.rows.push( { tr: el, text: null } );
					return;
				}
				var fs = el.querySelector( 'fieldset.redux-field' );
				var th = el.querySelector( ':scope > th' );
				var parts = [ th ? th.textContent : '', fs.getAttribute( 'data-id' ) || '' ];
				var desc = fs.querySelectorAll( '.field-desc, .description, .redux-info-desc, h3, label' );
				desc.forEach( function ( d ) {
					parts.push( d.textContent );
				} );
				entry.rows.push( { tr: el, text: norm( parts.join( ' ' ) ), group: group } );
			} );
			index.push( entry );
		} );
	}

	function clearSearch() {
		if ( $root.hasClass( 'zc-searching' ) ) {
			updateCurrent();
		}
		$root.removeClass( 'zc-searching zc-no-hits' );
		$root.find( '.zc-hit' ).removeClass( 'zc-hit' );
		$root.find( '.zc-miss' ).removeClass( 'zc-miss' );
		$menu.find( '.zc-menu-hit' ).removeClass( 'zc-menu-hit' );
	}

	function runSearch() {
		var raw = $search.val();
		var q = norm( raw );
		if ( q.length < 2 ) {
			clearSearch();
			return;
		}
		if ( ! index ) {
			buildIndex();
		}
		var words = q.split( ' ' );
		var total = 0;
		clearSearch();
		$root.addClass( 'zc-searching' );
		$( '#zc-current-section' ).text( cfg.searchLabel || '' );

		index.forEach( function ( entry ) {
			var hits = 0;
			var titleHit = words.every( function ( w ) {
				return entry.title.indexOf( w ) > -1;
			} );
			entry.rows.forEach( function ( row ) {
				var ok = false;
				if ( null !== row.text ) {
					var hay = row.text + ' ' + row.group + ' ' + entry.title;
					ok = words.every( function ( w ) {
						return hay.indexOf( w ) > -1;
					} );
					// عنوان بخش به‌تنهایی کافی نیست؛ دست‌کم یک واژه باید در خود ردیف یا گروهش باشد.
					if ( ok && titleHit ) {
						ok = words.some( function ( w ) {
							return ( row.text + ' ' + row.group ).indexOf( w ) > -1;
						} ) || entry.rows.length < 4;
					}
				}
				if ( ok ) {
					hits++;
				} else {
					row.tr.classList.add( 'zc-miss' );
				}
			} );
			// سرفصل گروه‌ها فقط وقتی دست‌کم یک ردیفِ پیداشده دارند دیده شوند.
			entry.tab.querySelectorAll( '.redux-section-field' ).forEach( function ( head ) {
				var id = ( head.id || '' ).replace( /^section-/, '' );
				var table = id ? entry.tab.querySelector( '#section-table-' + id ) : null;
				var box = head.closest( '.indent-section-container' ) || head;
				var visible = table && table.querySelector( 'tr:not(.zc-miss):not(.zc-empty-row):not(.hide):not(.hidden)' );
				box.classList.toggle( 'zc-miss', ! visible );
			} );
			if ( hits ) {
				total += hits;
				entry.tab.classList.add( 'zc-hit' );
				$menu.find( '[id="' + entry.rel + '_section_group_li"]' ).addClass( 'zc-menu-hit' )
					.parents( 'li.redux-group-tab-link-li' ).addClass( 'zc-menu-hit' );
			}
		} );

		$root.toggleClass( 'zc-no-hits', 0 === total );
	}

	function openTab( rel ) {
		$search.val( '' );
		clearSearch();
		var $a = $menu.find( '[id="' + rel + '_section_group_li"] > a' );
		if ( $a.length ) {
			$a.trigger( 'click' );
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		}
	}

	/* ------------------------------------------------------------ */
	/* کارت‌های پالت                                                  */
	/* ------------------------------------------------------------ */
	function syncPalette() {
		var val = $( '#palette_preset-select' ).val() || $( '[name="zc_options[palette_preset]"]' ).val();
		$root.find( '.zc-pal-card' ).each( function () {
			var active = this.getAttribute( 'data-preset' ) === val;
			this.classList.toggle( 'is-active', active );
			this.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
		} );
	}

	/* ------------------------------------------------------------ */
	/* شمارنده‌ی نویسه                                                */
	/* ------------------------------------------------------------ */
	var countMap = { seo_home_title: 60, seo_home_desc: 155 };

	function initCounters() {
		Object.keys( countMap ).forEach( function ( id ) {
			$( '#zc_options-' + id ).find( 'input[type="text"], textarea' ).first().attr( 'data-zc-count', countMap[ id ] );
		} );
		$root.find( '[data-zc-count]' ).each( function () {
			var $el = $( this );
			var max = parseInt( $el.attr( 'data-zc-count' ), 10 ) || 0;
			if ( ! max || $el.data( 'zcCounter' ) ) {
				return;
			}
			var $out = $( '<span class="zc-count" aria-live="polite"></span>' ).insertAfter( $el );
			var update = function () {
				var n = ( $el.val() || '' ).length;
				$out.text( n.toLocaleString( 'fa-IR' ) + ' / ' + max.toLocaleString( 'fa-IR' ) + ' ' + ( cfg.chars || '' ) );
				$out.toggleClass( 'is-over', n > max );
			};
			$el.data( 'zcCounter', true ).on( 'input', update );
			update();
		} );
	}

	/* ------------------------------------------------------------ */
	/* راه‌اندازی                                                     */
	/* ------------------------------------------------------------ */
	$( function () {
		$root = $( '.redux-container' ).first();
		if ( ! $root.length ) {
			return;
		}
		$menu = $root.find( '.redux-group-menu' );
		$search = $( '#zc-panel-search' );

		markEmptyRows();
		$root.find( '.redux-main' ).append( '<div class="zc-search-empty" role="status">' + ( cfg.noResults || '' ) + '</div>' );

		// عنوان بخش جاری
		updateCurrent();
		$menu.on( 'click', 'a.redux-group-tab-link-a', function () {
			if ( $search.val() ) {
				$search.val( '' );
				clearSearch();
			}
			setTimeout( updateCurrent, 30 );
		} );
		setTimeout( updateCurrent, 400 );

		// جستجو
		$search.on( 'input', function () {
			clearTimeout( timer );
			timer = setTimeout( runSearch, 140 );
		} ).on( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				$search.val( '' );
				clearSearch();
			} else if ( 'Enter' === e.key ) {
				e.preventDefault(); // فرم ذخیره نشود.
				var first = $root.find( '.redux-group-tab.zc-hit' ).first().attr( 'data-rel' );
				if ( first ) {
					openTab( first );
				}
			}
		} );
		$root.on( 'click', '.zc-searching .redux-group-tab.zc-hit > h2', function () {
			openTab( $( this ).closest( '.redux-group-tab' ).attr( 'data-rel' ) );
		} );
		// کلید میان‌بُر «/» برای جستجو
		$( document ).on( 'keydown', function ( e ) {
			if ( '/' === e.key && ! $( e.target ).is( 'input, textarea, select, [contenteditable]' ) ) {
				e.preventDefault();
				$search.trigger( 'focus' );
			}
		} );

		// میان‌بُرهای پیشخوان
		$root.on( 'click', '[data-zc-goto]', function ( e ) {
			e.preventDefault();
			var id = this.getAttribute( 'data-zc-goto' );
			var $a = $menu.find( 'li.zc-sec-' + id + ' > a' ).first();
			if ( $a.length ) {
				$search.val( '' );
				clearSearch();
				$a.trigger( 'click' );
				window.scrollTo( { top: 0, behavior: 'smooth' } );
			}
		} );

		// کارت‌های پالت → فیلد palette_preset
		$root.on( 'click', '.zc-pal-card', function ( e ) {
			e.preventDefault();
			var preset = this.getAttribute( 'data-preset' );
			var $sel = $( '#palette_preset-select' );
			if ( ! $sel.length ) {
				$sel = $( 'select[name="zc_options[palette_preset]"]' );
			}
			if ( $sel.length ) {
				$sel.val( preset ).trigger( 'change' );
			} else {
				$( 'input[name="zc_options[palette_preset]"][value="' + preset + '"]' ).prop( 'checked', true ).trigger( 'change' );
			}
			syncPalette();
		} );
		$root.on( 'change', '#palette_preset-select, [name="zc_options[palette_preset]"]', syncPalette );
		syncPalette();

		initCounters();
	} );
} )( jQuery );
