/**
 * Editor UIs for the WordPress Research Fellows home-page blocks.
 *
 * No build step: this registers each server-rendered block with a sidebar
 * (InspectorControls) for editing copy and a live ServerSideRender preview, so
 * the home page can be seen and edited from the admin dashboard. The front-end
 * markup is produced entirely by the PHP render callbacks in inc/blocks.php.
 */
( function ( blocks, element, blockEditor, components, serverSideRender, i18n ) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var InspectorControls = blockEditor.InspectorControls;
	var useBlockProps = blockEditor.useBlockProps;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;
	var ToggleControl = components.ToggleControl;
	var SelectControl = components.SelectControl;
	var ServerSideRender = serverSideRender;
	var __ = i18n.__;

	// Field types: text | textarea | toggle | lines | select.
	var BLOCKS = [
		{
			name: 'nu/hero',
			title: __( 'Fellows: Hero', 'nu-research' ),
			description: __( 'Full-width hero with eyebrow, heading, lead, and a call-to-action button.', 'nu-research' ),
			icon: 'cover-image',
			fields: [
				{ key: 'eyebrow', label: __( 'Eyebrow', 'nu-research' ), type: 'text' },
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'lead', label: __( 'Lead paragraph', 'nu-research' ), type: 'textarea' },
				{ key: 'ctaLabel', label: __( 'Button label', 'nu-research' ), type: 'text' },
				{ key: 'ctaSlug', label: __( 'Button links to page slug', 'nu-research' ), type: 'text' },
				{ key: 'image', label: __( 'Background image filename (assets/img/)', 'nu-research' ), type: 'text' }
			]
		},
		{
			name: 'nu/section-header',
			title: __( 'Fellows: Section Header', 'nu-research' ),
			description: __( 'Eyebrow, heading, and intro paragraph — the program overview.', 'nu-research' ),
			icon: 'heading',
			fields: [
				{ key: 'eyebrow', label: __( 'Eyebrow', 'nu-research' ), type: 'text' },
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'intro', label: __( 'Intro paragraph', 'nu-research' ), type: 'textarea' }
			]
		},
		{
			name: 'nu/media-card',
			title: __( 'Fellows: Media Card', 'nu-research' ),
			description: __( 'Image beside a heading and paragraph. Optionally reversed.', 'nu-research' ),
			icon: 'align-pull-left',
			fields: [
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'body', label: __( 'Body', 'nu-research' ), type: 'textarea' },
				{ key: 'image', label: __( 'Image filename (assets/img/)', 'nu-research' ), type: 'text' },
				{ key: 'imageAlt', label: __( 'Image alt text', 'nu-research' ), type: 'text' },
				{ key: 'reverse', label: __( 'Image on the right', 'nu-research' ), type: 'toggle' },
				{
					key: 'section',
					label: __( 'Background', 'nu-research' ),
					type: 'select',
					options: [
						{ label: __( 'Tight (white)', 'nu-research' ), value: 'section section-tight' },
						{ label: __( 'Muted (grey)', 'nu-research' ), value: 'section section-muted' }
					]
				}
			]
		},
		{
			name: 'nu/track-badges',
			title: __( 'Fellows: Track Badges', 'nu-research' ),
			description: __( 'Row of outlined badges listing the research tracks.', 'nu-research' ),
			icon: 'tag',
			fields: [
				{ key: 'label', label: __( 'Accessible label', 'nu-research' ), type: 'text' },
				{ key: 'tracks', label: __( 'Tracks (one per line)', 'nu-research' ), type: 'lines' }
			]
		},
		{
			name: 'nu/cta-band',
			title: __( 'Fellows: CTA Band', 'nu-research' ),
			description: __( 'Closing call-to-action with heading, lead, and button.', 'nu-research' ),
			icon: 'megaphone',
			fields: [
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'lead', label: __( 'Lead', 'nu-research' ), type: 'text' },
				{ key: 'ctaLabel', label: __( 'Button label', 'nu-research' ), type: 'text' },
				{ key: 'ctaSlug', label: __( 'Button links to page slug', 'nu-research' ), type: 'text' }
			]
		},
		{
			name: 'nu/hero-billboard',
			title: __( 'Fellows: Hero Billboard', 'nu-research' ),
			description: __( 'Full-black billboard hero: eyebrow, serif heading, lead, CTA, and side photo.', 'nu-research' ),
			icon: 'cover-image',
			fields: [
				{ key: 'eyebrow', label: __( 'Eyebrow', 'nu-research' ), type: 'text' },
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'lead', label: __( 'Lead paragraph', 'nu-research' ), type: 'textarea' },
				{ key: 'ctaLabel', label: __( 'Button label', 'nu-research' ), type: 'text' },
				{ key: 'ctaSlug', label: __( 'Button links to page slug', 'nu-research' ), type: 'text' },
				{ key: 'image', label: __( 'Image filename (assets/img/)', 'nu-research' ), type: 'text' },
				{ key: 'imageAlt', label: __( 'Image alt text', 'nu-research' ), type: 'text' }
			]
		},
		{
			name: 'nu/pillars',
			title: __( 'Fellows: Commitment Pillars', 'nu-research' ),
			description: __( 'Section heading and intro over a three-up icon / title / body grid.', 'nu-research' ),
			icon: 'columns',
			fields: [
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'intro', label: __( 'Intro paragraph', 'nu-research' ), type: 'textarea' },
				{ key: 'items', label: __( 'Items (one per line: icon|title|body)', 'nu-research' ), type: 'lines' }
			]
		},
		{
			name: 'nu/ambition',
			title: __( 'Fellows: Ambition Banner', 'nu-research' ),
			description: __( 'Black band: photo collage with a red stat tile beside eyebrow, serif heading, and lead.', 'nu-research' ),
			icon: 'awards',
			fields: [
				{ key: 'eyebrow', label: __( 'Eyebrow', 'nu-research' ), type: 'text' },
				{ key: 'heading', label: __( 'Heading', 'nu-research' ), type: 'text' },
				{ key: 'lead', label: __( 'Lead paragraph', 'nu-research' ), type: 'textarea' },
				{ key: 'statValue', label: __( 'Stat value (e.g. #1)', 'nu-research' ), type: 'text' },
				{ key: 'statCaption', label: __( 'Stat caption', 'nu-research' ), type: 'textarea' },
				{ key: 'imagePrimary', label: __( 'Large image filename (assets/img/)', 'nu-research' ), type: 'text' },
				{ key: 'imagePrimaryAlt', label: __( 'Large image alt text', 'nu-research' ), type: 'text' },
				{ key: 'imageSecondary', label: __( 'Small image filename (assets/img/)', 'nu-research' ), type: 'text' },
				{ key: 'imageSecondaryAlt', label: __( 'Small image alt text', 'nu-research' ), type: 'text' }
			]
		},
		{
			name: 'nu/journey-cards',
			title: __( 'Fellows: Journey Cards', 'nu-research' ),
			description: __( 'Labelled three-up grid of photo cards with arrow links.', 'nu-research' ),
			icon: 'grid-view',
			fields: [
				{ key: 'label', label: __( 'Section heading', 'nu-research' ), type: 'text' },
				{ key: 'cards', label: __( 'Cards (one per line: image|alt|title|body|ctaLabel|slug)', 'nu-research' ), type: 'lines' }
			]
		}
	];

	function renderControl( field, attributes, setAttributes ) {
		var value = attributes[ field.key ];

		if ( field.type === 'toggle' ) {
			return el( ToggleControl, {
				key: field.key,
				label: field.label,
				checked: !! value,
				onChange: function ( next ) {
					var change = {};
					change[ field.key ] = next;
					setAttributes( change );
				}
			} );
		}

		if ( field.type === 'select' ) {
			return el( SelectControl, {
				key: field.key,
				label: field.label,
				value: value,
				options: field.options,
				onChange: function ( next ) {
					var change = {};
					change[ field.key ] = next;
					setAttributes( change );
				}
			} );
		}

		if ( field.type === 'lines' ) {
			return el( TextareaControl, {
				key: field.key,
				label: field.label,
				value: ( value || [] ).join( '\n' ),
				onChange: function ( next ) {
					var change = {};
					change[ field.key ] = next.split( '\n' ).map( function ( line ) {
						return line.trim();
					} ).filter( function ( line ) {
						return line.length > 0;
					} );
					setAttributes( change );
				}
			} );
		}

		var Control = field.type === 'textarea' ? TextareaControl : TextControl;
		return el( Control, {
			key: field.key,
			label: field.label,
			value: value,
			onChange: function ( next ) {
				var change = {};
				change[ field.key ] = next;
				setAttributes( change );
			}
		} );
	}

	BLOCKS.forEach( function ( def ) {
		blocks.registerBlockType( def.name, {
			apiVersion: 2,
			title: def.title,
			description: def.description,
			icon: def.icon,
			category: 'nu-research',
			edit: function ( props ) {
				var controls = def.fields.map( function ( field ) {
					return renderControl( field, props.attributes, props.setAttributes );
				} );

				return el(
					Fragment,
					{},
					el(
						InspectorControls,
						{},
						el( PanelBody, { title: __( 'Content', 'nu-research' ), initialOpen: true }, controls )
					),
					el(
						'div',
						useBlockProps(),
						el( ServerSideRender, { block: def.name, attributes: props.attributes } )
					)
				);
			},
			save: function () {
				return null;
			}
		} );
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.serverSideRender,
	window.wp.i18n
);
