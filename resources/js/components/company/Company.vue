<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Company List</h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                                    <i class="fa fa-plus-square"></i>
                                    Add New
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="company in company.data" :key="company.id">
                                    <td>{{company.id}}</td>
                                    <td>{{company.name}}</td>
                                    <td>
                                        <a href="#" @click="editModal(company)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteCompany(company.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination :data="company" @pagination-change-page="getResults"></pagination>
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
                            <h5 class="modal-title" v-show="!editmode">Create New Company</h5>
                            <h5 class="modal-title" v-show="editmode">Edit Company</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updateCompany() : createCompany()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input v-model="form.name" type="text" name="name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <div v-show="editmode && form.brands.length > 0">
                                        <label>Selected brands:</label>
                                        <ul class="list-group-item" v-for="brand in form.brands" :key="brand.name">
                                            <li>{{brand.name}}</li>
                                        </ul>
                                    </div>
                                    <label>Brands:</label>
                                    <vue-good-table
                                        ref="brand_table"
                                        :columns="this.brand_table.cols"
                                        :rows="this.brand_table.rows"
                                        :pagination-options="{
                                                    enabled: true,
                                                    mode: 'records',
                                                    perPage: 10,
                                                    position: 'top',
                                                    perPageDropdown: [10, 20, 50],
                                                    dropdownAllowAll: false,
                                                    setCurrentPage: 1,
                                                    nextLabel: 'next',
                                                    prevLabel: 'prev',
                                                    rowsPerPageLabel: 'Brand per page',
                                                    ofLabel: 'of',
                                                    pageLabel: 'page', // for 'pages' mode
                                                    allLabel: 'All',
                                                  }"
                                        :select-options="{
                                                    enabled: true,
                                                    selectionInfoClass: 'custom-class',
                                                    selectionText: 'store(s) selected',
                                                    clearSelectionText: 'clear',
                                                    selectAllByGroup: true, // when used in combination with a grouped table, add a checkbox in the header row to check/uncheck the entire group
                                                  }"
                                        @on-selected-rows-change="onBrandSelected"
                                    />
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
    import vSelect from "vue-select";
    import {VueGoodTable} from "vue-good-table";

    export default {
        components: {
            FileUpload,
            VueGoodTable,
        },
        data() {
            return {
                editmode: false,
                company: {},
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
                        }
                    ],
                    rows: []
                },
                form: new Form({
                    id: '',
                    name: '',
                    brands: [],
                })
            }
        },
        methods: {
            onBrandSelected(params) {
                // params.selectedRows - all rows that are selected (this page)
                this.form.brands = params.selectedRows;
            },

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

                axios.get('api/company/list?page=' + page).then(({data}) => (this.company = data.data));

                this.$Progress.finish();
            },

            loadBrands() {
                // if(this.$gate.isAdmin()){
                axios.get("api/brands/list").then((data)=> {
                    console.log(data.data);
                    this.brand_table.rows = data.data.data;
                });
                // }
            },

            loadCompany() {

                // if(this.$gate.isAdmin()){
                axios.get("api/company/list").then(({data}) => (this.company = data.data));
                // }
            },
            editModal(company) {
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(company);
                this.form.brands = company.brands;

                // Object.values(company.brands).forEach((item) => {
                //     item.vgtSelected = true;
                // })

                this.loadBrands();

                // let brand_ids = Object.values(company.brands).map((b) => {
                //     return b.id
                // })
                //
                // if (brand_ids.length>0) {
                //     axios.get("api/brands/list?brand_ids=" + brand_ids).then((data) => {
                //         this.brand_table.rows = data.data.data;
                //         Object.svalues(this.brand_table.rows).forEach((row) => {
                //             row.vgtSelected = true;
                //         })
                //     });
                // } else {
                //     axios.get("api/brands/list").then((data) => {
                //         this.brand_table.rows = data.data.data;
                //     });
                // }
            },
            newModal() {
                this.editmode = false;
                this.form.reset();
                this.loadBrands();
                $('#addNew').modal('show');
            },
            createCompany() {
                this.$Progress.start();

                console.log(this.form);
                this.form.post('api/company')
                    .then((response) => {
                        console.log(response);
                        if (response.data.success) {
                            $('#addNew').modal('hide');

                            Toast.fire({
                                icon: 'success',
                                title: response.data.message
                            });
                            this.$Progress.finish();
                            this.loadCompany();

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
            updateCompany() {
                this.$Progress.start();
                this.form.put('api/company/' + this.form.id)
                    .then((response) => {
                        // success
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: response.data.message
                        });
                        this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                        this.loadCompany();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });

            },
            deleteCompany(id) {
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
                        this.form.delete('api/company/' + id).then(() => {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            // Fire.$emit('AfterCreate');
                            this.loadCompany();
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

            this.loadCompany();
            this.loadBrands();

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
