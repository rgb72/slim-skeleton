<template>
    <div
        :is="component"
        :type="type"
        :main.sync="localMain"
        :desktop.sync="localDesktop"
        :mobile.sync="localMobile"
        :lists="lists"
        :listName="listName"
        :listValue="listValue"
    ></div>
</template>

<script>
import TextView from './Text'
import TextareaView from './Textarea'
import FileView from './File'
import CheckboxView from './Checkbox'
import SelectView from './Select'
import CodeView from './Code'
import TextEditorView from './TextEditor'

export default {
    components: {
        TextView,
        TextareaView,
        FileView,
        CheckboxView,
        SelectView,
        CodeView,
        TextEditorView
    },
    props: {
        type: {
            type: String,
            default: 'text'
        },
        lists: {
            type: Array,
            required: false
        },
        listName: {
            type: String,
            default: 'name',
            required: false
        },
        listValue: {
            type: String,
            default: 'value',
            required: false
        },
        main: {
            required: true
        },
        desktop: {
            default: undefined,
            required: false
        },
        mobile: {
            default: undefined,
            required: false
        }
    },
    computed: {
        hasAdvance () {
            return this.localDesktop !== undefined && this.localMobile !== undefined
        },
        localMain: {
            get () {
                return this.main
            },
            set (value) {
                this.$emit('update:main', value)
                this.$emit('update:desktop', value)
                this.$emit('update:mobile', value)
            }
        },
        localDesktop: {
            get () {
                return this.desktop
            },
            set (value) {
                this.$emit('update:main', value)
                this.$emit('update:desktop', value)
            }
        },
        localMobile: {
            get () {
                return this.mobile
            },
            set (value) {
                this.$emit('update:main', value)
                this.$emit('update:mobile', value)
            }
        },
        component () {
            switch (this.type) {
                case 'file':
                case 'image':
                    return 'file-view'
                case 'checkbox':
                    return 'checkbox-view'
                case 'textarea':
                    return 'textarea-view'
                case 'select':
                    return 'select-view'
                case 'editor':
                    return 'text-editor-view'
                case 'twig':
                case 'html':
                case 'css':
                case 'js':
                    return 'code-view'
                default:
                    return 'text-view'
                break
            }
        }
    }
}
</script>

<style lang="sass">
@import ../../variables

// bulma/elements/form.sass
$input-color: $grey-darker !default
$input-background-color: $white !default
$input-border-color: $grey-lighter !default
$input-shadow: inset 0 1px 2px rgba($black, 0.1) !default

$input-hover-color: $grey-darker !default
$input-hover-border-color: $grey-light !default

$input-focus-color: $grey-darker !default
$input-focus-border-color: $link !default
$input-focus-box-shadow-size: 0 0 0 0.125em !default
$input-focus-box-shadow-color: rgba($link, 0.25) !default

$input-disabled-color: $text-light !default
$input-disabled-background-color: $background !default
$input-disabled-border-color: $background !default

$input-arrow: $link !default

$input-icon-color: $grey-lighter !default
$input-icon-active-color: $grey !default

$input-radius: $radius !default

$file-border-color: $border !default
$file-radius: $radius !default

$file-cta-background-color: $white-ter !default
$file-cta-color: $grey-dark !default
$file-cta-hover-color: $grey-darker !default
$file-cta-active-color: $grey-darker !default

$file-name-border-color: $border !default
$file-name-border-style: solid !default
$file-name-border-width: 1px 1px 1px 0 !default
$file-name-max-width: 16em !default

$label-color: $grey-darker !default
$label-weight: $weight-bold !default

$help-size: $size-small !default

=input
  +control
  background-color: $input-background-color
  border-color: $input-border-color
  color: $input-color
  +placeholder
    color: rgba($input-color, 0.3)
  &:hover,
  &.is-hovered
    border-color: $input-hover-border-color
  &:focus,
  &.is-focused,
  &:active,
  &.is-active
    border-color: $input-focus-border-color
    box-shadow: $input-focus-box-shadow-size $input-focus-box-shadow-color
  &[disabled]
    background-color: $input-disabled-background-color
    border-color: $input-disabled-border-color
    box-shadow: none
    color: $input-disabled-color
    +placeholder
      color: rgba($input-disabled-color, 0.3)
// end bulma

.wrapper-input
    +input
    box-shadow: $input-shadow
    max-width: 100%
    width: 100%
    display: block
    min-height: 2.25em
    height: auto

    .control
        &.is-fullwidth
            width: 100%

    .image
        display: flex
        justify-content: center
        align-items: center
        margin: auto

    &:not(.advance)
        padding: 0
        .input, .textarea
            border: none
            box-shadow: none

    &.advance
        padding: 1em
        .field
            + .field
                margin-top: .75rem

        .label
            font-weight: normal

        .input, .textarea
            border-style: dashed
            border-width: 1px
            border-top: none
            border-left: none
            border-right: none
            box-shadow: none

    &.checkbox
        // background-color: rgba($black, 0.1)
        padding-left: .5em
        padding-right: .5em
        &:not(.advance)
            display: flex
        &.advance
            .field
                + .field
                    border-top: 1px dashed #dbdbdb
</style>
