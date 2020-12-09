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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <vue-good-table
                                        ref="company_table"
                                        :columns="this.company_table.cols"
                                        :rows="this.company_table.rows"
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
                                                <a href="#" @click.prevent="deleteCompany(props.row)">
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
                                    <div v-show="form.brands.length > 0">
                                        <label> {{form.brands.length}} Selected brands:</label>
                                        <ul v-for="brand in form.brands" :key="brand.name">
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
                                            disableSelectInfo: true, // disable the select info panel on top
                                        }"
                                        >
                                        <template slot="table-row" slot-scope="props">
                                            <span v-if="props.column.field == 'actions'">
                                                <button class="btn btn-sm primary" @click.prevent="onAddBrandClick(props.row)"><i class="fa fa-plus-circle"></i> add</button>
                                                <button class="btn btn-sm primary" @click.prevent="onRemoveBrandClick(props.row)"><i class="fa fa-minus-circle"></i> remove</button>
                                            </span>
                                            <span v-else>
                                              {{props.formattedRow[props.column.field]}}
                                            </span>
                                        </template>

                                    </vue-good-table>
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

    export default {
        components: {
            FileUpload,
            VueGoodTable,
        },
        data() {
            return {
                editmode: false,
                company: [],
                company_table: {
                    cols: [
                        {
                            label: 'id',
                            field: 'id',
                        },
                        {
                            label: 'name',
                            field: 'name',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'Actions',
                            field: 'actions'
                        },
                    ],
                    rows: [],
                },
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
                            label: 'Actions',
                            field: 'actions'
                        },
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
            onRemoveBrandClick(row) {
                let idx = -1
                for (let i = 0; i < this.form.brands.length; i++) {
                    if (this.form.brands[i].id == row.id) {
                        idx = i;
                        break;
                    }
                }
                console.log("idx " + idx);
                if (idx != -1) {
                    this.form.brands.splice(idx, 1);
                    Toast.fire({
                        heading: 'Information',
                        icon: "info",
                        title: row.name + " removed!"
                    })
                }
                console.log(this.form.brands)
            },

            onAddBrandClick(row) {
                let idx = -1
                for (let i = 0; i < this.form.brands.length; i++) {
                    if (this.form.brands[i].id == row.id) {
                        idx = i;
                        break;
                    }
                }
                console.log("idx " + idx);
                if (idx == -1) {
                    this.form.brands.push(row);
                    Toast.fire({
                        heading: 'Success',
                        icon: "success",
                        title: row.name + " added!"
                    })
                } else {
                    Toast.fire({
                        heading: 'Error',
                        icon: "error",
                        title: row.name + " added already!"
                    });

                    this.$Progress.fail();
                }
                console.log(this.form.brands)
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
                axios.get("api/company/list?all=true").then(({data}) => (this.company_table.rows = data.data));
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
            deleteCompany(company) {
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
                        this.form.delete('api/company/' + company.id).then(() => {
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
