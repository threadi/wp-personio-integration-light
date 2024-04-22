wp.blocks.registerBlockVariation(
  'core/query',
  {
    name: 'personio-integration-archive-view',
    title: 'Personio Integration Archive View',
    description: 'Displays a list of positions.',
    attributes: {
      query: {
        namespace: 'personio-integration-archive-view',
        postType: 'personioposition'
      }
    },
    isDefault: false,
    isActive: [ 'personio-integration-archive-view' ],
    innerBlocks: [
      [
        'core/post-template',
        {},
        [
          [ 'core/post-title' ],
          [ 'wp-personio-integration/details' ],
          [ 'wp-personio-integration/application-button' ]
        ],
      ]
    ]
  }
);
