<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <vue-good-table
                        ref="position_table"
                        :columns="this.utilities_table.cols"
                        :rows="this.utilities_table.rows"
                        :pagination-options="{
                            enabled: true,
                            mode: 'records',
                            perPage: 10,
                            position: 'top',
                            perPageDropdown: [10, 20, 40, 70, 100],
                            dropdownAllowAll: false,
                            setCurrentPage: 1,
                            nextLabel: 'next',
                            prevLabel: 'prev',
                            rowsPerPageLabel: 'Positions per page',
                            ofLabel: 'of',
                            pageLabel: 'page', // for 'pages' mode
                            allLabel: 'All',
                        }"
                        :select-options="{
                            disableSelectInfo: true, // disable the select info panel on top
                        }"
                    >
                    </vue-good-table>
                </div>
            </div>
        </div>
    </section>
</template>

<script>

// import the styles
import 'vue-good-table/dist/vue-good-table.css'
import {VueGoodTable} from 'vue-good-table';

export default {
    name: "Utilities",
    components: {
        VueGoodTable
    },
    data() {
        return {
            utilities_table: {
                cols: [
                    {
                        label: 'ID',
                        field: 'id',
                        type: 'number',
                    },
                    {
                        label: 'Type',
                        field: 'type_name',
                        filterOptions: {
                            enabled: true, // enable filter for this column
                        }
                    },
                    {
                        label: 'Name',
                        field: 'name',
                        filterOptions: {
                            enabled: true, // enable filter for this column
                        }
                    },
                    {
                        label: 'Current Step',
                        field: 'current_step',
                        filterOptions: {
                            enabled: true, // enable filter for this column
                        }
                    },
                    {
                        label: 'Result',
                        field: 'result',
                    },
                    {
                        label: 'Created At',
                        field: 'created_at',
                        type: 'date',
                        dateInputFormat: 't', // expects 2018-03-16
                        dateOutputFormat: 'MMM do yyyy', // outputs Mar 16th 2018

                    },
                ],
                rows: [],
            },
        }
    },
    methods: {
        loadUtilities(params = {}) {
            axios.get("api/v1/utilities", {
                params: params
            }).then(({data}) => {
                this.utilities_table.rows = data.data.items;
            });
        }
    },
    created() {
        this.loadUtilities();
    },
}
</script>

<style scoped>

</style>
