<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">campaign List</h3>
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
                                    <th>Contract</th>
<!--                                    <th>Status</th>-->
                                    <th>License Code</th>
                                    <th>Brand</th>
                                    <th>Positions</th>

                                    <th>From</th>
                                    <th>To</th>

                                    <th>Discount Type</th>
                                    <th>Discount Value</th>
                                    <th>Discount Max</th>
                                    <th>Total  Discount</th>
                                    <th>Total  Price</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="campaign in campaigns.data" :key="campaign.id">
                                    <th>{{campaign.id}}</th>
                                    <th>{{campaign.name}}</th>
                                    <th>{{campaign.contract_code}}</th>
                                    <!--                                    <th>Status</th>-->
                                    <th>{{campaign.license_code}}</th>
                                    <th>{{campaign.brand.name}}</th>
                                    <th>
                                        <ul>
                                            <li v-for="item in campaign.position_list" :key="item.id">
                                                {{ item.name }}
                                            </li>
                                        </ul>
                                    </th>

                                    <th>{{campaign.from_ts}}</th>
                                    <th>{{campaign.to_ts}}</th>

                                    <th>{{campaign.discount_type}}</th>
                                    <th>{{campaign.discount_value}}</th>
                                    <th>{{campaign.discount_max}}</th>
                                    <th>{{campaign.total_discount}}</th>
                                    <th>{{campaign.total_price}}</th>
                                    <td>
                                        <a href="#" @click="editModal(campaign)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deletecampaign(campaign.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination :data="campaigns" @pagination-change-page="getResults"></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">Create New campaign</h5>
                            <h5 class="modal-title" v-show="editmode">Edit campaign</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updateCampaign() : createCampaign()">
                            <div class="modal-body">
                                <div class="row" v-show="!editmode">
                                    <div class="col-md-12">
                                        <div class="text-lg-left text-info">
                                            <label>Filter by store by location:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-show="!editmode">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Province/City</label>
                                                <v-select v-model="store_filter.province_id" label="name" :options="provinces.data" @input="onProvinceChange"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>District</label>
                                                <v-select v-model="store_filter.district_id" label="name" :options="districts.data" @input="onDistrictChange"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Ward</label>
                                                <v-select v-model="store_filter.ward_id" label="name" :options="wards.data"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Store Level</label>
                                                <v-select v-model="store_filter.store_level" label="name" :options="[{id:'A', name: 'A'},{id:'B', name: 'B'}]"></v-select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <a class="btn btn-primary" href="#" @click="searchStores">Search Store</a>
                                            </div>
                                        </div>
                                    </div>

                                <div class="form-group">
                                    <label>Store</label>

                                    <v-select multiple v-model="position_filter.store_ids" label="name" :options="stores.data"
                                            :reduce="store => store.id"
                                              :class="{ 'is-invalid': form.errors.has('store')}"></v-select>

                                    <has-error :form="form" field="store"></has-error>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Channel</label>
                                            <v-select multiple v-model="position_filter.channels" label="name" :options="channels.data"
                                              :reduce="channel => channel.name"
                                                  :class="{ 'is-invalid': form.errors.has('channel')}"></v-select>
                                            <has-error :form="form" field="channel"></has-error>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>From :</label>
                                            <date-picker v-model="position_filter.from_ts" :config="datetimepicker.options"></date-picker>
                                        </div>
                                        <div class="col-md-4">
                                            <label>To :</label>
                                            <date-picker v-model="position_filter.to_ts" :config="datetimepicker.options"></date-picker>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <a class="btn btn-primary" href="#" @click="searchPositions">Search Positions</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Positions</label>
                                            <v-select multiple v-model="form.positions" label="name" :options="positions.data">
                                            </v-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><b>Price: {{ 'position_price'}}</b></label>
                                </div>
                                <div class="form-group">
                                    <label>Campaign Name:</label>
                                    <input v-model="form.name" type="text" name="name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Contract code:</label>
                                    <input v-model="form.contract_code" type="text"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('contract_code') }">
                                    <has-error :form="form" field="contract_code"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>License code:</label>
                                    <input v-model="form.license_code" type="text"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('license_code') }">
                                    <has-error :form="form" field="license_code"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Description:</label>
                                    <input v-model="form.description" type="text" name="description"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Unit:</label>
                                    <v-select v-model="form.unit" label="name" :options="units.data"
                                        :reduce="unit => unit.id"
                                        :class="{ 'is-invalid': form.errors.has('unit') }"></v-select>
                                    <has-error :form="form" field="unit"></has-error>
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

    export default {
        components: {
            FileUpload,
            vSelect
        },
        data () {
            return {
                editmode: false,
                campaigns : {},
                channels: {},
                provinces: {},
                districts: {},
                positions: {},
                wards: {},
                stores: {},
                // statuses: {
                //     'AVAILABLE': 'Available',
                //     'RESERVED': 'Reserved',
                //     'RUNNING': 'Running',
                // },

                store_filter: {
                    province_id: '',
                    district_id: '',
                    ward_id: '',
                    store_level: '',
                },

                position_filter: {
                    store_ids: '',
                    channels: '',

                    from_ts: new Date(),
                    to_ts: new Date(),

                },

                datetimepicker:{
                    options: {
                      format: 'MM/DD/YYYY',
                      useCurrent: false
                    }
                },

                units: {
                    data: [
                        { id: 'day', name: 'Day'},
                        { id: 'week', name: 'Week'},
                        { id: 'month', name: 'Month'},
                    ]
                },
                form: new Form({
                    id : '',
                    name: '',
                    contract_code: '',
                    license_code: '',
                    status: '',
                    brand_id: '',
                    booking_id: '',
                    positions: [],
                    channels: [],
                    from_ts: '',
                    to_ts: '',
                    discount_type: '',
                    discount_value: '',
                    discount_max:'',
                    total_discount:'',
                    total_price:''
                }),

                // fileUploaded: [],
                headers: {},
                // fileUploadUrl: '/file/upload'
                // autocompleteItems: [],
            }
        },
        methods: {
            searchStores() {
                this.stores = [];
                console.log(this.store_filter);
                let params = [];
                Object.entries(this.store_filter).forEach((item) => {
                        if(item[1] && typeof item[1]['id'] !== 'undefined') {
                            params.push(item[0] + '=' + item[1]['id']);
                        }
                    }
                );
                console.log(params.join('&'));
                axios.get("api/stores/list?"+params.join('&')).then((data)=> {
                    console.log(data.data);
                    this.stores = data.data;
                });
            },
            searchPositions(){
                this.positions = [];
                console.log(this.position_filter);
                let params = [];
                Object.entries(this.position_filter).forEach((item) => {
                        console.log(item);
                        console.log(Date.parse(item[1]));
                        if(item[1] && typeof item[1] !== 'undefined') {
                            if (item[0] == "from_ts" || item[0] == "to_ts") {
                                params.push(item[0] + '=' + parseInt(Date.parse(item[1])/1000));
                            } else {
                                params.push(item[0] + '=' + item[1]);
                            }
                        }
                    }
                );
                console.log(params.join('&'));

                axios.get("api/positions/list?"+params.join('&')).then((data)=> {
                    console.log(data.data);
                    this.positions = data.data;
                });
            },
            onProvinceChange(province) {
                this.districts = {};
                this.wards = {};
                axios.get("api/districts/list?province_id="+province.id).then(({ data }) => (this.districts = data.data));
            },
            onDistrictChange(district) {
                this.wards = {};
                axios.get("api/wards/list?district_id="+district.id).then(({ data }) => (this.wards = data.data));
            },
            onWardChange(ward) {
                axios.get("api/stores/list?ward_id="+ward.id).then(({ data }) => (this.stores = data.data));
            },
            onChannelChange(channels) {
                console.log(channels);
                // axios.get("api/positions/list?channels="+channels).then(({ data }) => {
                //     this.positions = data.data
                // });
            },
            // onFileChange(e) {
            //     const file = e.target.files[0];
            //     console.log(file);
            //     const formData = new FormData();
            //     formData.append('file', file);
            //     axios.post('/file/upload', formData).then(({data}) => {
            //         console.log(data)
            //         this.form.image_url = data.file_path;
            //     });
            // },

            getResults(page = 1) {

                this.$Progress.start();

                axios.get('api/campaigns?page=' + page).then(({ data }) => (this.campaigns = data.data));

                this.$Progress.finish();
            },
            loadCampaigns(){

                // if(this.$gate.isAdmin()){
                axios.get("api/campaigns").then(({ data }) => (this.campaigns = data.data));
                // }
            },

            loadChannels(){
                // if(this.$gate.isAdmin()){
                axios.get("api/channels/list").then(({ data }) => {
                    this.channels = data.data
                });
                // }
            },

            loadProvinces(){
                // if(this.$gate.isAdmin()){
                axios.get("api/provinces/list").then(({ data }) => (this.provinces = data.data));
                // }
            },
            loadDistricts(){

                // if(this.$gate.isAdmin()){
                axios.get("api/districts/list").then(({ data }) => (this.districts = data.data));
                // }
            },
            loadWards(){

                // if(this.$gate.isAdmin()){
                axios.get("api/wards/list").then(({ data }) => (this.wards = data.data));
                // }
            },

            loadStores(){

                // if(this.$gate.isAdmin()){
                axios.get("api/stores/list").then(({ data }) => (this.stores = data.data));
                // }
            },

            loadPositions(){

                // if(this.$gate.isAdmin()){
                axios.get("api/positions/list").then(({ data }) => (this.positions = data.data));
                // }
            },

            editModal(campaign){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(campaign);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            createCampaign(){
                this.$Progress.start();

                console.log(this.form);
                this.form.post('api/campaigns')
                    .then((data)=>{
                        console.log(data.data);
                        if(data.data.success){
                            $('#addNew').modal('hide');

                            Toast.fire({
                                icon: 'success',
                                title: data.data.message
                            });
                            this.$Progress.finish();
                            this.loadCampaigns();

                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: data.data.message
                            });

                            this.$Progress.fail();
                        }
                    }).catch( error =>{
                        Toast.fire({
                            icon: 'error',
                            title: error
                        });
                        this.$Progress.fail();
                    })
            },
            updateCampaign(){
                this.$Progress.start();
                if (typeof this.form.store === 'object') {
                    this.form.store = this.form.store.id;
                }

                console.log(this.form.store);

                this.form.put('api/campaigns/'+this.form.id)
                    .then((response) => {
                        // success
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: response.data.message
                        });
                        this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                        this.loadCampaigns();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });

            },
            deletecampaign(id){
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
                        this.form.delete('api/campaigns/'+id).then(()=>{
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            // Fire.$emit('AfterCreate');
                            this.loadCampaigns();
                        }).catch((data)=> {
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

            this.loadCampaigns();
            this.loadProvinces();
            this.loadDistricts();
            this.loadWards();
            this.loadStores();
            this.loadChannels();
            this.loadPositions();

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
