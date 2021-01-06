<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Position List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-dark" @click="importModal">
                                    <i class="fa fa-file-import"></i>
                                    Import
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                                    <i class="fa fa-plus-square"></i>
                                    Add New
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>From :</label>
                                            <date-picker v-model="filter_table.from_ts" :config="datetimepicker.options"></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>To :</label>
                                            <date-picker v-model="filter_table.to_ts" :config="datetimepicker.options"></date-picker>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary" @click="filterDate">Filter</button>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <vue-good-table
                                        ref="position_table"
                                        :columns="this.position_table.cols"
                                        :rows="this.position_table.rows"
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
                                                <a href="#" @click.prevent="deletePosition(props.row)">
                                                    <i class="fa fa-trash red"></i>
                                                </a>
                                            </span>
                                            <span v-else-if="props.column.field === 'date_booking'">
                                                <span class="badge badge-danger" v-if="props.row.bookings.filter(item => item.from_ts <= props.column.date_value && item.to_ts >= props.column.date_value && item.campaign.status === 'booked').length > 0">Booked</span>
                                                <span class="badge badge-success" v-else-if="props.row.bookings.filter(item => item.from_ts <= props.column.date_value && item.to_ts >= props.column.date_value && item.campaign.status === 'reserved').length > 0">Reserved</span>
                                                <span class="badge badge-light" v-else>Available</span>
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

            <!-- import modal -->
            <div class="modal fade" id="importPositions" tabindex="-1" role="dialog" aria-labelledby="importPositions"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select a file to import positions</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Import position</label>
                                <input type="file" name="file" @change="onFileChange" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <button type="button" class="btn btn-sm btn-primary" @click="importPositions"
                                        id="importPositionsBtn">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">Create New Position</h5>
                            <h5 class="modal-title" v-show="editmode">Edit Position</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updatePosition() : createPosition()">
                            <div class="modal-body">
                                <div class="row" v-show="!editmode">
                                    <div class="col-md-12">
                                        <div class="text-lg-left text-info">
                                            <label>Filter by store by location:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-show="!editmode">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Province/City</label>

                                            <v-select v-model="form.province" label="name" :options="provinces.data"
                                                      @input="onProvinceChange"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>District</label>
                                            <v-select v-model="form.district" label="name" :options="districts.data"
                                                      @input="onDistrictChange"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ward</label>
                                            <v-select v-model="form.ward" label="name" :options="wards.data"
                                                      @input="onWardChange"></v-select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Store</label>

                                    <v-select v-model="form.store" label="name" :options="stores.data"
                                              :class="{ 'is-invalid': form.errors.has('store')}"></v-select>
                                    <has-error :form="form" field="store"></has-error>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Channel</label>
                                            <v-select v-model="form.channel" label="name" :options="channels.data"
                                                      :reduce="channel => channel.name"
                                                      @input="onChannelChange"
                                                      :class="{ 'is-invalid': form.errors.has('channel')}"></v-select>
                                            <has-error :form="form" field="channel"></has-error>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Buffer Days:</label>
                                            <input v-model="form.buffer_days" type="text" name="buffer_days"
                                                   class="form-control"
                                                   :class="{ 'is-invalid': form.errors.has('buffer_days')}">
                                            <has-error :form="form" field="buffer_days"></has-error>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input v-model="form.name" type="text" name="name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <input v-model="form.description" type="text" name="description"
                                           class="form-control"
                                           :class="{ 'is-invalid': form.errors.has('description') }">
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Unit:</label>
                                    <v-select v-model="form.unit" label="name" :options="units.data"
                                              :reduce="unit => unit.id"
                                              :class="{ 'is-invalid': form.errors.has('unit') }"></v-select>
                                    <has-error :form="form" field="unit"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Price:</label>
                                    <input v-model="form.price" type="text" name="price"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('price') }">
                                    <has-error :form="form" field="price"></has-error>
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
import "vue-select/dist/vue-select.css";
import FileUpload from 'v-file-upload';
import vSelect from "vue-select";

// import the styles
import 'vue-good-table/dist/vue-good-table.css'
import {VueGoodTable} from 'vue-good-table';

export default {
    components: {
        FileUpload,
        vSelect,
        VueGoodTable
    },
    data() {
        return {
            editmode: false,
            positions: {},
            channels: {},
            provinces: {},
            districts: {},
            wards: {},
            stores: {},
            // statuses: {
            //     'AVAILABLE': 'Available',
            //     'RESERVED': 'Reserved',
            //     'RUNNING': 'Running',
            // },
            units: {
                data: [
                    {id: 'day', name: 'Day'},
                    // { id: 'week', name: 'Week'},
                    // { id: 'month', name: 'Month'},
                ]
            },
            form: new Form({
                id: '',
                name: '',
                description: '',
                // status: '',
                image_url: '',
                store: '',
                channel: '',
                buffer_days: '',
                unit: '',
                price: '',
            }),

            // fileUploaded: [],
            headers: {},
            positionFileUrl: '',

            position_table: {
                cols: [

                ],
                fixed_cols: [
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
                        label: 'channel',
                        field: 'channel',
                        filterOptions: {
                            enabled: true, // enable filter for this column
                        }
                    },
                    {
                        label: 'price',
                        field: 'price',
                    },
                    {
                        label: 'unit',
                        field: 'unit',
                    },
                    {
                        label: 'buffer_days',
                        field: 'buffer_days',
                    },
                    {
                        label: 'Actions',
                        field: 'actions'
                    },
                ],
                flex_cols: [
                    {
                        label: this.getDateString((new Date())),
                        field: 'date_booking',
                        date_value: parseInt(Date.now() / 1000),
                    }
                ],
                rows: [],
            },
            filter_table: {
                from_ts: null,
                to_ts: null,
            },
            datetimepicker:{
                options: {
                    format: 'MM/DD/YYYY',
                    useCurrent: false
                }
            },
        }
    },
    methods: {
        onProvinceChange(province) {
            this.districts = {};
            this.wards = {};
            axios.get("api/districts/list?province_id=" + province.id).then(({data}) => (this.districts = data.data));
        },
        onDistrictChange(district) {
            this.wards = {};
            axios.get("api/wards/list?district_id=" + district.id).then(({data}) => (this.wards = data.data));
        },
        onWardChange(ward) {
            this.stores = {};
            axios.get("api/stores/list?ward_id=" + ward.id).then(({data}) => (this.stores = data.data));
        },
        onChannelChange(channel) {
            this.channels.data.forEach(item => {
                    if (item['name'] == channel) {
                        this.form.buffer_days = item['buffer_days'];
                        return;
                    }
                }
            )

        },
        onFileChange(e) {
            const file = e.target.files[0];
            console.log(file);
            const formData = new FormData();
            formData.append('file', file);
            axios.post('/file/upload?type=positions', formData).then(({data}) => {
                console.log(data)
                this.positionFileUrl = data.file_path;
            });
        },

        getResults(page = 1) {

            this.$Progress.start();

            axios.get('api/positions?page=' + page).then(({data}) => (this.position_table.rows = data.data.data));

            this.$Progress.finish();
        },
        loadPositions() {
            // let params =  {
            //     from_ts: this.filter_table.from_ts ? parseInt(Date.parse(this.filter_table.from_ts)/1000) : null,
            //     to_ts: this.filter_table.to_ts ? parseInt(Date.parse(this.filter_table.to_ts)/1000) : null,
            // };
            // if(this.$gate.isAdmin()){
            axios.get("api/positions/list/v2", {
                // params: params
            }).then(({data}) => {
                this.position_table.rows = data.data;
            });
            // }
        },

        loadChannels() {
            // if(this.$gate.isAdmin()){
            axios.get("api/channels/list").then(({data}) => {
                this.channels = data.data
            });
            // }
        },

        loadProvinces() {
            // if(this.$gate.isAdmin()){
            axios.get("api/provinces/list").then(({data}) => (this.provinces = data.data));
            // }
        },
        loadDistricts() {

            // if(this.$gate.isAdmin()){
            axios.get("api/districts/list").then(({data}) => (this.districts = data.data));
            // }
        },
        loadWards() {

            // if(this.$gate.isAdmin()){
            axios.get("api/wards/list").then(({data}) => (this.wards = data.data));
            // }
        },

        loadStores() {

            // if(this.$gate.isAdmin()){
            axios.get("api/stores/list").then(({data}) => (this.stores = data.data));
            // }
        },

        editModal(position) {
            console.log(position);
            this.editmode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(position);
        },
        importModal() {
            $('#importPositions').modal('show');
        },

        importPositions() {
            if (this.positionFileUrl) {
                var _this = this;
                _this.$Progress.start();
                $('#importPositionsBtn').prop('disabled', true);
                axios.post('api/positions/import', {
                    filePath: this.positionFileUrl,
                })
                    .then(function (response) {
                        $('#importPositionsBtn').prop('disabled', false);

                        if (response.status == 200) {
                            $('#importPositions').modal('hide');
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            _this.$Progress.finish();
                            _this.loadPositions();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });

                            _this.$Progress.fail();
                        }
                    })
                    .catch(function (error) {
                        Toast.fire({
                            icon: 'error',
                            title: error
                        });
                        $('#importPositionsBtn').prop('disabled', false);
                        _this.$Progress.fail();
                    });
            }
        },
        newModal() {
            this.editmode = false;
            this.form.reset();
            $('#addNew').modal('show');
        },
        createPosition() {
            this.$Progress.start();

            console.log(this.form);
            this.form.post('api/positions')
                .then((data) => {
                    console.log(data.data);
                    if (data.data.success) {
                        $('#addNew').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: data.data.message
                        });
                        this.$Progress.finish();
                        this.loadPositions();

                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.data.message
                        });

                        this.$Progress.fail();
                    }
                }).catch(error => {
                Toast.fire({
                    icon: 'error',
                    title: error
                });
                this.$Progress.fail();
            })
        },
        updatePosition() {
            this.$Progress.start();
            // if (typeof this.form.store === 'object') {
            //     this.form.store = this.form.store.id;
            // }

            console.log(this.form.store);

            this.form.put('api/positions/' + this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                    this.$Progress.finish();
                    //  Fire.$emit('AfterCreate');

                    this.loadPositions();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

        },
        deletePosition(position) {
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
                    this.form.delete('api/positions/' + position.id).then(() => {
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        );
                        // Fire.$emit('AfterCreate');
                        this.loadPositions();
                    }).catch((data) => {
                        Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },
        getDateString(date) {
            return date.toLocaleDateString('en-EN');
        },
        filterDate() {
            let from_ts = parseInt(Date.parse(this.filter_table.from_ts)/1000);
            let to_ts = parseInt(Date.parse(this.filter_table.to_ts)/1000);

            if (from_ts > to_ts) {
                Toast.fire({
                    icon: 'warning',
                    type: 'danger',
                    title: 'From date cannot be after than To date'
                });
                return false;
            }

            let days = (to_ts - from_ts) / (24 * 60 * 60);
            if (days > 90) {
                Toast.fire({
                    icon: 'warning',
                    type: 'danger',
                    title: 'The interval between From and To date should not exceed 90'
                });
                return false;
            }

            let the_date = null, date_value = 0;
            let flex_cols = [];
            for (var i = 0; i <= days; i++)
            {
                date_value = from_ts + i * 24 * 60 * 60;
                the_date = new Date(date_value * 1000);
                flex_cols = flex_cols.concat({
                    label: this.getDateString(the_date),
                    field: 'date_booking',
                    date_value: date_value,
                })
            }
            console.log(flex_cols);

            this.position_table.flex_cols = flex_cols;
            this.position_table.cols = [].concat(this.position_table.fixed_cols).concat(this.position_table.flex_cols);
        }
    },
    mounted() {

    },
    created() {
        this.position_table.cols = [].concat(this.position_table.fixed_cols).concat(this.position_table.flex_cols);
        this.$Progress.start();

        this.loadPositions();
        this.loadProvinces();
        this.loadDistricts();
        this.loadWards();
        this.loadStores();
        this.loadChannels();

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
