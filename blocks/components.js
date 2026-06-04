import {__} from "@wordpress/i18n";
import { PanelBody, ExternalLink } from '@wordpress/components';

export const onChangeTitleVisibility = ( newValue, object ) => {
    object.setAttributes( { showTitle: newValue } );
}

export const onChangeExcerptVisibility = ( newValue, object ) => {
    object.setAttributes( { showExcerpt: newValue } );
}

export const onChangeContentVisibility = ( newValue, object ) => {
    object.setAttributes({ showContent: newValue });
}

export const onChangeApplicationFormVisibility = ( newAlignment, object ) => {
    object.setAttributes( { showApplicationForm: newAlignment } );
}

export const onChangeLimit = ( newValue, object ) => {
    if( newValue.length === 0 ) {
        newValue = 0;
    }
    object.setAttributes({ limit: newValue });
}

export const onChangeSort = ( newValue, object ) => {
    object.setAttributes( { sort: newValue } );
}

export const onChangeSortBy = ( newValue, object ) => {
    object.setAttributes( { sortby: newValue } );
}

export const onChangeGroupBy = ( newValue, object ) => {
    object.setAttributes( { groupby: newValue } );
}

export const onChangeExcerptTemplates = ( newValue, object ) => {
    object.setAttributes( { excerptTemplates: newValue } );
}

export const onChangeId = ( newValue, object ) => {
    object.setAttributes( { id: newValue } );
}

export const onChangeLinkingTitle = ( newValue, object ) => {
    object.setAttributes( { linkTitle: newValue } );
}

export const onChangeFilter = ( newValue, object ) => {
    object.setAttributes( { filter: newValue } );
}

export const onChangeFilterType = ( newValue, object ) => {
    object.setAttributes( { filtertype: newValue } );
}

export const onChangeShowFilter = ( newValue, object ) => {
    object.setAttributes( { showFilter: newValue } );
}

export const onChangeHideResetLink = ( newValue, object ) => {
    object.setAttributes( { hideResetLink: newValue } );
}

export const onChangeHideFilterTitle = ( newValue, object ) => {
    object.setAttributes( { hideFilterTitle: newValue } );
}

export const onChangeHideSubmitButton = ( newValue, object ) => {
    object.setAttributes( { hideSubmitButton: newValue } );
}

export const onChangeSpaceBetween = ( newValue, object ) => {
    object.setAttributes( { space_between: newValue } );
}

export const onChangeTemplate = ( newValue, object ) => {
	object.setAttributes( { template: newValue } );
}

export const onChangeLinkToAnchor = ( newValue, object ) => {
  object.setAttributes( { link_to_anchor: newValue } );
}

export const onChangePositionBackgroundColor = ( value, object ) => {
  object.setAttributes( { positionBackgroundColor: value } );
}

export const onChangePositionBackgroundColorHover = ( value, object ) => {
  object.setAttributes( { positionBackgroundColorHover: value } );
}

/**
 * Panel for helper texts.
 */
export class Personio_Helper_Panel extends React.Component {
  render() {
    return (
      window.personio_integration_config.enable_help && <PanelBody initialOpen={false} title={ __( 'Do you need help?', 'personio-integration-light' ) }>
        <p>{__( 'You are welcome to contact our support forum if you have any questions.', 'personio-integration-light' )}</p>
        <p>{<ExternalLink href={ window.personio_integration_config.support_url }>{ __( 'Go to supportforum', 'personio-integration-light' ) }</ExternalLink>}</p>
      </PanelBody>
    )
  }
}

const el = wp.element.createElement;
export const personioIcon = el('img', {src: ' data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABhWlDQ1BJQ0MgcHJvZmlsZQAAKJF9kb9Lw0AcxV/TaotUHOwg4pChOlkQK+IoVSyChdJWaNXB5NJf0MSQpLg4Cq4FB38sVh1cnHV1cBUEwR8g/gHipOgiJX4vKbSI8eC4D+/uPe7eAUKzxlQzMAGommVkkgkxX1gRg6/oRQAhCIhLzNRT2YUcPMfXPXx8vYvxLO9zf45+pWgywCcSzzLdsIjXiac3LZ3zPnGEVSSF+Jx43KALEj9yXXb5jXPZYYFnRoxcZo44QiyWu1juYlYxVOIp4qiiapQv5F1WOG9xVmt11r4nf2G4qC1nuU5zBEksIoU0RMioo4oaLMRo1UgxkaH9hId/2PGnySWTqwpGjnlsQIXk+MH/4He3Zik+6SaFE0DPi21/jALBXaDVsO3vY9tunQD+Z+BK6/g3msDMJ+mNjhY9Aga2gYvrjibvAZc7wNCTLhmSI/lpCqUS8H5G31QABm+BvlW3t/Y+Th+AHHW1dAMcHAJjZcpe83h3qLu3f8+0+/sBSnFyl3LH5EwAAAAGYktHRAA2AE0AXiOzlDkAAAAJcEhZcwAADdcAAA3XAUIom3gAAAAHdElNRQfqBgQIBQlxke3qAAABo0lEQVRIx83WTYhNYRgH8N+DrZUFjUzyUays7KTUtZmUhdS4gyxkwZadlJWVDUlZKSWW1poonynNApthc2tErGRc3evmtXnTmeOcOffOdMuzec/7nOf5/9/zPl8nUkrJGGWNMcuKCSJiKiJuRsRcRPQiQkTMR8ShJXajXFFETOAczmADXmAWb9HFNA6nlNb/dUpDCHbhDnro4AI2VtgdR2+Jbgjwq+jjJY7iBLaW7TqdTiuT3xqKIAN9xgLaBf1PHCna9vv925jDu39wasDvY4ArJf0UBoPB4HFBtxvzeIOJRgK8xlccrHh3HU8L+xl8w6PamygBPMsZMVnzZe9xsdvtXsY9/MKlZWNYcD6F79hSA74DCdfwBR+wrylJioU2ibU4UFMG03k9jRsppe0ppSeNxVM6ZRs7C/vNOIbn+fSvsC2NIBpqYDEH8W4mOJlGlCaCTXndj99VaThKDKqu71N+bOUi+jiubtrCw3G2671lgohoR8SPkbJomVicramL802+Me6Rua5iqMysEnMxpfSgkiAi9uROuhpZWPHI/C//Kv4AmQdH64cSClkAAAAASUVORK5CYII='});
