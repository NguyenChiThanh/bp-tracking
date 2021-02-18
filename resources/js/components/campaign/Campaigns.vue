<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Campaign List</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#importCampaignModal">
                                    <i class="fa fa-file-import"></i>
                                    Import
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" @click="newModal" v-if="$gate.isMod()">
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
                                    <th>Status</th>
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
                                    <td>{{campaign.id}}</td>
                                    <td>{{campaign.name}}</td>
                                    <td>{{campaign.contract_code}}</td>
                                    <td v-html="campaign.status_html"></td>
                                    <td>{{campaign.license_code}}</td>
                                    <td>{{campaign.brand.name}}</td>
                                    <td>
                                        {{ campaign.position_list.length }}
                                    </td>

                                    <td>{{campaign.from_ts}}</td>
                                    <td>{{campaign.to_ts}}</td>

                                    <td>{{campaign.discount_type}}</td>
                                    <td>{{campaign.discount_value}}</td>
                                    <td>{{campaign.discount_max}}</td>
                                    <td>{{campaign.total_discount}}</td>
                                    <td>{{campaign.total_price}}</td>
                                    <td v-if="$gate.isMod()">
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
                            <pagination :data="campaigns" :limit="2" @pagination-change-page="getResults"></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <!-- Modal -->
            <div v-if="$gate.isMod()" class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
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
                                <div class="form-group">
                                    <label>Store</label>
                                    <vue-good-table
                                                  :columns="this.store_table.cols"
                                                  :rows="this.store_table.rows"
                                                  :pagination-options="{
                                                    enabled: true,
                                                    mode: 'records',
                                                    perPage: 5,
                                                    position: 'top',
                                                    perPageDropdown: [5, 10, 20, 50],
                                                    dropdownAllowAll: false,
                                                    setCurrentPage: 1,
                                                    nextLabel: 'next',
                                                    prevLabel: 'prev',
                                                    rowsPerPageLabel: 'Store per page',
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
                                                  @on-selected-rows-change="onStoreSelected"
                                            />

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
                                            <date-picker v-model="position_filter.from_ts" :config="datetimepicker.options"
                                                         :class="{ 'is-invalid': form.errors.has('from_ts')}"></date-picker>
                                            <has-error :form="form" field="from_ts"></has-error>
                                        </div>
                                        <div class="col-md-4">
                                            <label>To :</label>
                                            <date-picker v-model="position_filter.to_ts" :config="datetimepicker.options"
                                                         :class="{ 'is-invalid': form.errors.has('to_ts')}"></date-picker>
                                            <has-error :form="form" field="to_ts"></has-error>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Status :</label>
                                            <v-select v-model="form.status" label="text" :options="statuses.data"
                                                      :class="{ 'is-invalid': form.errors.has('status.value')} "></v-select>
                                            <has-error :form="form" field="status.value"></has-error>
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
                                            <vue-good-table
                                                ref="position_table"
                                                :columns="this.position_table.cols"
                                                :rows="this.position_table.rows"
                                                :pagination-options="{
                                                enabled: true,
                                                mode: 'records',
                                                perPage: 5,
                                                position: 'top',
                                                perPageDropdown: [5, 10, 20, 50],
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
                                                        <button class="btn btn-sm primary" @click.prevent="onAddPositionClick(props.row)"><i class="fa fa-plus-circle"></i> add</button>
                                                        <button class="btn btn-sm primary" @click.prevent="onRemovePositionClick(props.row)"><i class="fa fa-minus-circle"></i> remove</button>
                                                    </span>
                                                    <span v-else>
                                                      {{props.formattedRow[props.column.field]}}
                                                    </span>
                                                 </template>
                                        </vue-good-table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class="text-alert"><b>Price: {{ form.position_price}}</b> for {{form.position_list.length}} position(s) x {{form.days_diff}} day(s)</span>
                                    </div>
                                    <div class="row">
                                        <ul class="list-inline">
                                          <li v-for="pos in form.position_list" :key="pos.id">
                                            {{ pos.name }}
                                          </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Discount type:</label>
                                            <v-select v-model="form.discount_type" :options="['flat', 'percentage']"></v-select>
                                        </div>
                                         <div class="col-md-4">
                                            <span class="text-sm">Discount value: (for eg: 0.2 for percentage discount type/ 100000 for flat discount type)</span>
                                            <input type="number" min="0" step="any" v-model="form.discount_value" class="form-control" @input="computeDiscount">
                                         </div>

                                         <div class="col-md-4" v-show="form.discount_type === 'percentage'">
                                            <label>Discount max:</label>
                                            <input type="number" min="0" v-model="form.discount_max" class="form-control" @input="computeDiscount">
                                         </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-control">Total discount:</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input readonly v-model="form.total_discount" class="form-control">
                                        </div>
                                         <div class="col-md-3">
                                            <label class="form-control text-success">Total price:</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input readonly v-model="form.total_price" class="form-control text-success">
                                        </div>
                                    </div>
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
                                    <label>Company:</label>
                                    <v-select v-model="form.company" label="name"
                                              :options="company.data"
                                              @input="onCompanyChange"></v-select>
                                </div>
                                <div class="form-group">
                                    <label>Brand:</label>
                                    <v-select v-model="form.brand" label="name" :options="brands.data"
                                        :class="{ 'is-invalid': form.errors.has('brand.id')} "></v-select>
                                    <has-error :form="form" field="brand.id"></has-error>
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

            <!-- import modal -->
            <div class="modal fade" id="importCampaignModal" tabindex="-1" role="dialog" aria-labelledby="importCampaignModal"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select a file to import positions</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Import Campaign</label>
                                <input type="file" name="file" @change="onFileChange" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">
                                <button type="button" class="btn btn-sm btn-primary" @click="importCampaign"
                                        id="importCampaignBtn">
                                    Submit
                                </button>
                            </div>
                        </div>
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
    import { VueGoodTable } from 'vue-good-table';
    import moment from "moment";

    export default {
        components: {
            FileUpload,
            vSelect,
            VueGoodTable
        },
        data () {
            return {
                editmode: false,
                campaigns : {},
                channels: {},
                provinces: {},
                districts: {},
                company: {},
                brands: {},
                statuses: [],
                position_table: {
                    cols: [
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
                            label: 'Store Name',
                            field: 'store_name',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'Channel',
                            field: 'channel',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'Price',
                            field: 'price',
                            type: 'number',
                        },
                        {
                            label: 'Buffer days',
                            field: 'buffer_days',
                            type: 'number',
                        },
                        {
                          label: 'Actions',
                          field: 'actions'
                        },
                    ],
                    rows: [],
                },

                store_table: {
                    cols: [
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
                            label: 'Level',
                            field: 'level',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'Ward',
                            field: 'ward',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                        {
                            label: 'District',
                            field: 'district',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                         {
                            label: 'Province',
                            field: 'province',
                            filterOptions: {
                                enabled: true, // enable filter for this column
                            }
                        },
                    ],
                    rows: [],
                },

                wards: {},
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
                    company: {},
                    brand: {},
                    position_list: [],
                    days_diff: 0,
                    position_price: 0,
                    from_ts: '',
                    to_ts: '',
                    discount_type: 'flat',
                    discount_value: 0,
                    discount_max:0,
                    total_discount:0,
                    total_price:0
                }),

                // fileUploaded: [],
                headers: {},
                // fileUploadUrl: '/file/upload'
                // autocompleteItems: [],
                importCampaignFile: null,
            }
        },
        methods: {
            computeTotalPrice(){
                this.form.total_price = this.form.position_price - this.form.total_discount;
            },
            computeDiscount(){
                console.log(this.form.discount_type);
                if(this.form.discount_type === 'flat') {
                    this.form.total_discount = this.form.discount_value;
                } else {
                    console.log(this.form.discount_type);
                    if(this.form.discount_value * this.form.position_price > this.form.discount_max) {
                        this.form.total_discount = this.form.discount_max;
                    } else {
                        this.form.total_discount = this.form.discount_value * this.form.position_price;
                    }
                }
                this.computeTotalPrice();
            },
            searchStores() {
                this.store_table.rows = {};
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
                    this.store_table.rows = data.data;
                });
            },

            onRemovePositionClick(row) {
                let idx = -1
                for (let i = 0; i < this.form.position_list.length; i++) {
                    if (this.form.position_list[i].id == row.id) {
                        idx = i;
                        break;
                    }
                }
                console.log("idx " + idx);
                if (idx != -1) {
                    this.form.position_list.splice(idx, 1);
                    this.form.position_price -= row.price * this.form.days_diff;
                    Toast.fire({
                         heading: 'Information',
                        icon: "info",
                        title: row.name + " removed!"
                    })
                }
                console.log(this.form.position_list)
                this.computeDiscount();
            },

            onAddPositionClick(row) {
                let idx = -1
                for (let i = 0; i < this.form.position_list.length; i++) {
                    if (this.form.position_list[i].id == row.id) {
                        idx = i;
                        break;
                    }
                }
                console.log("idx " + idx);
                if (idx == -1) {
                    this.form.position_list.push(row);
                    this.form.position_price += row.price * this.form.days_diff;
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
                console.log(this.form.position_list)
                this.computeDiscount();
            },

            onStoreSelected(params) {
                // params.selectedRows - all rows that are selected (this page)
                this.position_filter.store_ids = [];
                Object.values(params.selectedRows).forEach((item) => {
                    this.position_filter.store_ids.push(item.id);
                });
            },

            calculateDaysDiff(){
                return Math.ceil(
                        (
                            Date.parse(this.position_filter.to_ts) - Date.parse(this.position_filter.from_ts)
                        ) /
                        (24*60*60*1000)
                    ) + 1;
            },

            searchPositions(){
                this.form.days_diff = this.calculateDaysDiff();
                this.position_table.rows = [];
                // this.form.position_list = [];

                console.log(this.position_filter);

                let params = [];
                Object.entries(this.position_filter).forEach((item) => {
                        console.log(item);
                        console.log(Date.parse(item[1]));
                        if(item[1] && typeof item[1] !== 'undefined') {
                            if (item[0] == "from_ts" || item[0] == "to_ts") {
                                let ts = parseInt(Date.parse(item[1])/1000);
                                params.push(item[0] + '=' + ts);
                                // set this.form.from_ts and this.form.to_ts
                                this.form[item[0]] = ts;
                            } else {
                                params.push(item[0] + '=' + item[1]);
                            }
                        }
                    }
                );

                axios.get("api/positions/list?"+params.join('&')).then((data)=> {
                    console.log(data.data);
                    this.position_table.rows = data.data.data;
                });
            },
            onProvinceChange(province) {

                let ids = [];
                Object.values(province).forEach((item) => {
                    ids.push(item.id);
                })

                this.districts = {};
                this.wards = {};
                axios.get("api/districts/list?province_id="+province.id).then(({ data }) => (this.districts = data.data));
            },

            onCompanyChange(company) {
                this.brands = {};
                axios.get("api/brands/list?company_id="+company.id).then((response) => {
                    this.brands = response.data
                })
            },

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

            loadStores() {
                // if(this.$gate.isAdmin()){
                 axios.get("api/stores/list").then(({ data }) => {
                     this.store_table.rows = data.data.data
                 });
                // }
            },

            loadCompany() {
                // if(this.$gate.isAdmin()){
                axios.get("api/company/list?all=true").then((response)=> {
                    this.company = response.data;
                });
                // }
            },

            loadBrands() {
                // if(this.$gate.isAdmin()){
                axios.get("api/brands/list").then((data)=> {
                    console.log(data.data);
                    this.brands = data.data;
                });
                // }
            },
            loadStatuses() {
                // if(this.$gate.isAdmin()){
                axios.get("api/v1/statuses/list").then((data)=> {
                    console.log(data.data);
                    this.statuses = data.data;
                });
                // }
            },
            editModal(campaign){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');

                this.form.fill(campaign);
                console.log("brand " + JSON.stringify(this.form));

                // this.form.company['id'] = campaign.brand.company_id;
                // this.form.brand.id = campaign.brand.id;

                this.position_filter.from_ts = campaign.from_ts;
                this.position_filter.to_ts = campaign.to_ts;

                this.position_table.rows = campaign.position_list;
                this.form.position_list = campaign.position_list;
                Object.values(this.position_table.rows).forEach((row) => {
                    row.vgtSelected = true;
                })
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            createCampaign(){
                this.$Progress.start();

                this.form.from_ts = parseInt(Date.parse(this.position_filter.from_ts)/1000);
                this.form.to_ts = parseInt(Date.parse(this.position_filter.to_ts)/1000);
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

                this.form.from_ts = moment(this.position_filter.from_ts).startOf('date').unix();
                this.form.to_ts = moment(this.position_filter.to_ts).endOf('date').unix();
                console.log(this.form);

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
            onFileChange(e) {
                const file = e.target.files[0];
                console.log(file);
                this.importCampaignFile = file;
            },
            importCampaign() {
                var _this = this;
                _this.$Progress.start();

                const formData = new FormData();
                formData.append('file', this.importCampaignFile);
                formData.append('type', 'import_campaign');

                $('#importCampaignBtn').prop('disabled', true);
                axios.post('api/v1/utilities/import', formData)
                    .then(function (response) {
                        $('#importCampaignBtn').prop('disabled', false);

                        if (response.status === 200) {
                            $('#importCampaignModal').modal('hide');
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            _this.$Progress.finish();
                            _this.loadCampaigns();
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
                        $('#importCampaignBtn').prop('disabled', false);
                        _this.$Progress.fail();
                    });
            }
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
            this.loadCompany();
            this.loadBrands();
            this.loadStatuses();

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
