/**
 * Embed requirements.
 */
import { __ } from '@wordpress/i18n';
import { store as commandsStore } from '@wordpress/commands';
import { dispatch } from '@wordpress/data';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from "@wordpress/core-data";
import { useMemo } from "@wordpress/element";
import { addQueryArgs } from '@wordpress/url';
import { personioIcon } from "./components";

/**
 * Initiate the custom command to add positions.
 */
dispatch( commandsStore ).registerCommandLoader( {
  name: 'personio-integration-light/positions',
  hook: usePersonioPositionsInCommandPalette,
} );

/**
 * Define our custom command to load positions in the command palette.
 *
 * @param search
 * @returns {{commands: *, isLoading: *}}
 */
function usePersonioPositionsInCommandPalette( { search } ) {
  // Retrieve the pages for the "search" term.
  const { records, isLoading } = useSelect(
    (select) => {
      const { getEntityRecords } = select(coreStore);
      const query = {
        search: !!search ? search : undefined,
        per_page: 10,
        orderby: search ? "relevance" : "date",
      };
      return {
        records: getEntityRecords("postType", "personioposition", query),
        isLoading: !select(coreStore).hasFinishedResolution(
          "getEntityRecords",
          "postType",
          "page",
          query
        ),
      };
    },
    [search]
  );

  /**
   * Create the commands.
   */
  const commands = useMemo(() => {
    return (records ?? []).slice(0, 10).map((record) => {
      return {
        name: record.title?.rendered + " " + record.id,
        label: record.title?.rendered
          ? record.title?.rendered
          : __("(no title)"),
        icon: personioIcon,
        category: 'view',
        callback: ({ close }) => {
          const args = {
            action: 'edit',
            post: record.id
          };
          document.location = addQueryArgs("post.php", args);
          close();
        },
      };
    });
  }, [records, history]);

  return {
    commands,
    isLoading,
  };
}

/**
 * Initiate the custom command to add action to import positions.
 */
dispatch( commandsStore ).registerCommandLoader( {
  name: 'personio-integration-light/import',
  hook: addImportCommandInCommandPalette,
} );

/**
 * Define our custom action to import positions.
 *
 * @returns {{commands: *}}
 */
function addImportCommandInCommandPalette() {
  const commands = useMemo( () => {
     return [
      {
        name: 'personio-integration-light/import',
        label: __( 'Import positions', 'personio-integration-light' ),
        icon: personioIcon,
        callback: ( { close } ) => {
          close();
          personio_integration_light_get_import_dialog()
        },
      },
    ];
  }, [] );

  return { commands };
}
