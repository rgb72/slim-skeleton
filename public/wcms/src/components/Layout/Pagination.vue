<template>
    <div class="columns">
        <div class="column is-10">
            <nav class="pagination is-centered">
                <router-link :to="{query: previousPageQuery()}" :class="getPreviousPagingClass()">
                    Previous
                </router-link>

                <router-link :to="{query: nextPageQuery()}" :class="getNextPagingClass()">
                    Next
                </router-link>

                <ul class="pagination-list is-hidden-mobile">
                    <li v-for="item in pagingList">
                        <router-link v-if="item !== '...'" active-class="" :class="getPagingClass(item)" :to="{query: pageQuery(item)}">
                            {{ item }}
                        </router-link>
                        <span v-else class="pagination-ellipsis">...</span>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="column is-2">
            <div class="select is-fullwidth">
                <select v-model="currentPage" v-on:change="jumpTo">
                    <option v-for="page in lastPage" :value="page">{{ page }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        pagination: {
            default: null
        },
        displayLength: {
            default: 7
        },
        params: {
            default: null
        }
    },
    data() {
        return {
            currentPage: 1,
            lastPage: 1
        }
    },
    computed: {
        pagingList() {
            if(this.pagination === null) return []
            if(this.pagination.last_page <= 1) return []

            this.currentPage = this.pagination.current_page
            this.lastPage = this.pagination.last_page

            let displayLength = this.displayLength - 1

            let indexes = [1]
            let start = Math.round(this.pagination.current_page - displayLength / 2)
            let end = Math.round(this.pagination.current_page + displayLength / 2)

            if(start <= 1) {
                start = 2
                end = start + displayLength - 1
                if(end >= this.pagination.last_page - 1) end = this.pagination.last_page - 1
            }

            if(end >= this.pagination.last_page - 1) {
                end = this.pagination.last_page - 1
                start = end - displayLength + 1
                if(start <= 1) start = 2
            }

            if(start !== 2) indexes.push('...')

            for(let i = start; i <= end; i++) {
                indexes.push(i)
            }

            if(end !== this.pagination.last_page - 1) indexes.push('...')
            indexes.push(this.pagination.last_page)

            return indexes
        }
    },
    methods: {
        jumpTo() {
            let query = Object.assign({
                page: this.currentPage
            }, this.params)

            this.$router.push({
                query
            })
        },
        pageQuery(page) {
            if(this.pagination === null) return

            if(page <= 1) page = 1
            if(page > this.pagination.last_page) page = this.pagination.last_page

            let query = Object.assign({}, this.$route.query)

            query.page = page

            if(this.params) Object.assign(query, this.params)

            return query
        },
        previousPageQuery() {
            return this.pagination && this.pagination.current_page > 1
                    ? this.pageQuery(this.pagination.current_page - 1)
                    : null
        },
        nextPageQuery() {
            return this.pagination && this.pagination.current_page < this.pagination.last_page
                    ? this.pageQuery(this.pagination.current_page + 1)
                    : null
        },
        getPreviousPagingClass() {
            return this.pagination && this.pagination.current_page !== 1
                    ? 'pagination-previous'
                    : 'pagination-previous is-disabled'
        },
        getNextPagingClass() {
            return this.pagination && this.pagination.current_page < this.pagination.last_page
                    ? 'pagination-next'
                    : 'pagination-next is-disabled'
        },
        getPagingClass(page) {
            return this.pagination && this.pagination.current_page !== page
                    ? 'pagination-link'
                    : 'pagination-link is-current'
        }
    },
    watch: {
        '$route' (from, to) {
            window.scrollTo(0, 0)
        }
    }
}
</script>
