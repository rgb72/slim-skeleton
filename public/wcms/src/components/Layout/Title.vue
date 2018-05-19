<template>
    <ol class="breadcrumb">
        <li v-for="item in breadcrumbs">
            {{item}}
        </li>
    </ol>
</template>

<script>
    export default {
        props: ['breadcrumbs']
    }
</script>

<style lang="sass">
.breadcrumb
    --separator: "\2044"
    list-style: none
    align-item: center
    display: flex
    justify-content: flex-end

    > li
        + li:before
            padding: 0 5px
            color: #CCC
            content: var(--separator, "\2044")
</style>
