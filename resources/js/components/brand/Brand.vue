<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Brand List</h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                                    <i class="fa fa-plus-square"></i>
                                    Add New
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <vue-good-table
                                        ref="brand_table"
                                        :columns="this.brand_table.cols"
                                        :rows="this.brand_table.rows"
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
                                        <template slot="table-row" slot-scope="props">
                                            <span v-if="props.column.field == 'actions'">
                                                <a href="#" @click.prevent="editModal(props.row)">
                                                <i class="fa fa-edit blue"></i>
                                                </a>
                                                /
                                                <a href="#" @click.prevent="deleteBrand(props.row)">
                                                    <i class="fa fa-trash red"></i>
                                                </a>
                                            </span>
                                            <span v-else>
                                              {{props.formattedRow[props.column.field]}}
                                            </span>
                                        </template>
                                    </vue-good-table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">Create New Brand</h5>
                            <h5 class="modal-title" v-show="editmode">Edit Brand</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updateBrand() : createBrand()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input v-model="form.name" type="text" name="name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Company:</label>
                                    <v-select v-model="form.company" label="name" :options="company.data">
                                    </v-select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    // import VueTagsInput from '@johmun/vue-tags-input';
    import FileUpload from 'v-file-upload';
    import {VueGoodTable} from "vue-good-table";
    import vSelect from "vue-select";

    export default {
        components: {
            FileUpload,
            vSelect,
            VueGoodTable,
        },
        data() {
            return {
                editmode: false,
                company: '',
                brand_table: {
                    cols:[
                        {
                            label: 'ID',
                            field: 'id',
                            type: 'number',
                        },
                        {
                            label: 'Name',
                            field: 'name',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'Company',
                            field: 'company.name',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'Actions',
                            field: 'actions'
                        },
                    ],
                    rows: []
                },
                form: new Form({
                    id: '',
                    name: '',
                    company: '',
                })
            }
        },
        methods: {
            onFileChange(e) {
                const file = e.target.files[0];
                console.log(file);
                const formData = new FormData();
                formData.append('file', file);
                axios.post('/file/upload', formData).then(({data}) => {
                    console.log(data)
                    this.form.image_url = data.file_path;
                });
            },

            getResults(page = 1) {

                this.$Progress.start();

                axios.get('api/brands/list?page=' + page).then(({data}) => (this.company = data.data));

                this.$Progress.finish();
            },

            loadBrands() {
                // if(this.$gate.isAdmin()){
                axios.get("api/brands/list").then(({data}) => (this.brand_table.rows = data.data));
                // }
            },

            loadCompany() {

                // if(this.$gate.isAdmin()){
                axios.get("api/company/list?all=true").then(({data}) => (this.company = data.data));
                // }
            },
            editModal(brand) {
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(brand);
                // this.loadBrands();
            },
            newModal() {
                this.editmode = false;
                this.form.reset();
                // this.loadBrands();
                $('#addNew').modal('show');
            },
            createBrand() {
                this.$Progress.start();

                console.log(this.form);
                this.form.post('api/brands')
                    .then((response) => {
                        console.log(response);
                        if (response.data.success) {
                            $('#addNew').modal('hide');

                            Toast.fire({
                                icon: 'success',
                                title: response.data.message
                            });
                            this.loadBrands();
                            this.$Progress.finish();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Some error occured! Please try again'
                            });

                            this.$Progress.failed();
                        }
                    })
                    .catch((error) => {

                        console.log(error.response.data.errors);

                        Toast.fire({
                            icon: 'error',
                            title: 'Some error occured! Please try again'
                        });
                    })
            },
            updateBrand() {
                this.$Progress.start();
                this.form.put('api/brands/' + this.form.id)
                    .then((response) => {
                        // success
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: response.data.message
                        });

                        this.loadBrands();
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });

            },
            deleteBrand(brand) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    // Send request to the server
                    if (result.value) {
                        this.form.delete('api/brands/' + brand.id).then(() => {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            this.loadBrands();
                            this.$Progress.finish();
                        }).catch((data) => {
                            Swal.fire("Failed!", data.message, "warning");
                        });
                    }
                })
            },

        },
        mounted() {

        },
        created() {
            this.$Progress.start();

            this.loadBrands();
            this.loadCompany();

            this.$Progress.finish();
        },
        filters: {
            truncate: function (text, length, suffix) {
                return text.substring(0, length) + suffix;
            },
        },
        // computed: {
        //   filteredItems() {
        //     return this.autocompleteItems.filter(i => {
        //       return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
        //     });
        //   },
        // },
    }
</script>
