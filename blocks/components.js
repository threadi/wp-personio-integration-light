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

const el = wp.element.createElement;
export const iconEl = el('img', {src: ' data:image/png;base64,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OTExIgogICB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOmMyODgyODQxLTUyZTctNGYzZC1iM2E2LWUzMmE4MGM0ZTZiYiIKICAgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOmUyZThlMTI2LTIzNjAtNDkxMC1iMzdmLTM2ZWZhM2FmMjBlMSIKICAgZGM6Rm9ybWF0PSJpbWFnZS9wbmciCiAgIEdJTVA6QVBJPSIyLjAiCiAgIEdJTVA6UGxhdGZvcm09IldpbmRvd3MiCiAgIEdJTVA6VGltZVN0YW1wPSIxNjUyMTgyMzMyMDgzNDI1IgogICBHSU1QOlZlcnNpb249IjIuMTAuMzAiCiAgIHRpZmY6T3JpZW50YXRpb249IjEiCiAgIHhtcDpDcmVhdG9yVG9vbD0iR0lNUCAyLjEwIj4KICAgPHhtcE1NOkhpc3Rvcnk+CiAgICA8cmRmOlNlcT4KICAgICA8cmRmOmxpCiAgICAgIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiCiAgICAgIHN0RXZ0OmNoYW5nZWQ9Ii8iCiAgICAgIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6ODcxNzM1MmYtYTE0NC00YzA2LWE2NTctOTFiYWM1Mjc2NTMwIgogICAgICBzdEV2dDpzb2Z0d2FyZUFnZW50PSJHaW1wIDIuMTAgKFdpbmRvd3MpIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTA1LTEwVDEzOjMyOjEyIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAKPD94cGFja2V0IGVuZD0idyI/PlHPWCUAAAACYktHRAD/h4/MvwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB+YFCgsgDDvzukMAAADHSURBVBjTY/zPgAmYGIgVZEEw/946c/i5aoUYAwPDfyj4vitmzvVv/4+t+///P1Twz8Ho7Ud//v//u/weXPBlybKv/yJ+/v/RseM/TPB64t3//590/X+Rf/A/TPBp+of////v2rs35R7EMBYGBoaj9vwMDAxHTiZM5oA65P///5+u//98ps7/Gcwh/xkh3nx6iulbJMLJMNklV+EK/8O9uVuR4fkxNL//C+Nk+ANTwPgfV4BsWwXnh3nBVP5FKGdkxqUdayADALEPl4OIhY2rAAAAAElFTkSuQmCC'});