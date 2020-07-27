<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Position List</h3>
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
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                    <th>Store Level</th>
                                    <th>Store Name</th>
                                    <th>Channel</th>
                                    <th>Buffer days</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="position in positions.data" :key="position.id">

                                    <td>{{position.id}}</td>
                                    <td>{{position.name}}</td>
                                    <td>{{position.description}}</td>
                                    <td>{{position.status}}</td>
                                    <td><img v-bind:src="position.image_url" class="img-thumbnail img-fluid" width="20%" v-bind:alt="position.name +' image'"></td>
                                    <td>{{position.store.level}}</td>
                                    <td><a v-bind:href="'store/'+position.store.id">{{position.store.name}}</a></td>
                                    <td>{{position.channel}}</td>
                                    <td>{{position.buffer_days}}</td>
                                    <td>{{position.unit}}</td>
                                    <td>{{position.price}}</td>
                                    <td>
                                        <a href="#" @click="editModal(position)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deletePosition(position.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination :data="positions" @pagination-change-page="getResults"></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
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
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Province/City</label>

                                            <v-select v-model="form.province" label="name" :options="provinces.data" @input="onProvinceChange"></v-select>

<!--                                            <select class="form-control" v-model="form.province"-->
<!--                                                    :class="{ 'is-invalid': form.errors.has('province') }">-->
<!--                                                <option-->
<!--                                                    v-for="(province,index) in provinces.data" :key="index"-->
<!--                                                    :value="province.name"-->
<!--                                                    :selected="province.name == form.province">{{ province.name }}</option>-->
<!--                                            </select>-->
<!--                                            <has-error :form="form" field="province"></has-error>-->
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>District</label>
                                            <v-select v-model="form.district" label="name" :options="districts.data" @input="onDistrictChange"></v-select>

                                            <!--                                            <select class="form-control" v-model="form.district"-->
<!--                                                    :class="{ 'is-invalid': form.errors.has('district') }">-->
<!--                                                <option-->
<!--                                                    v-for="(district,index) in districts.data" :key="index"-->
<!--                                                    :value="district.name"-->
<!--                                                    :selected="district.name == form.district">{{ district.name }}</option>-->
<!--                                            </select>-->
<!--                                            <has-error :form="form" field="district"></has-error>-->
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ward</label>
                                            <v-select v-model="form.ward" label="name" :options="wards.data" @input="onWardChange"></v-select>

<!--                                            <select class="form-control" v-model="form.ward"-->
<!--                                                    :class="{ 'is-invalid': form.errors.has('ward') }">-->
<!--                                                <option-->
<!--                                                    v-for="(ward,index) in wards.data" :key="index"-->
<!--                                                    :value="ward.name"-->
<!--                                                    :selected="ward.name == form.ward">{{ ward.name }}</option>-->
<!--                                            </select>-->
<!--                                            <has-error :form="form" field="district"></has-error>-->
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Store</label>
                                    <v-select v-model="form.store" label="name" :options="stores.data"></v-select>

<!--                                    <select v-model="form.store_name" type="text" name="store_name"-->
<!--                                           class="form-control" :class="{ 'is-invalid': form.errors.has('store_name') }">-->
<!--                                    </select>-->
<!--                                    <has-error :form="form" field="store_name"></has-error>-->
                                </div>
                                <div class="form-group">
                                    <label>Channel</label>
                                    <v-select v-model="form.channel" label="name" :options="channels.data"></v-select>
                                    <has-error :form="form" field="channel"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input v-model="form.name" type="text" name="name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="file" @change="onFileChange" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <input v-model="form.description" type="text" name="description"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Buffer Days:</label>
                                    <input v-model="form.buffer_days" type="text" name="buffer_days"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('buffer_days') }">
                                    <has-error :form="form" field="buffer_days"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Unit:</label>
                                    <input v-model="form.unit" type="text" name="unit"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('unit') }">
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

    export default {
        components: {
            FileUpload,
            vSelect
        },
        data () {
            return {
                editmode: false,
                positions : {},
                channels: [],
                provinces: [],
                districts: [],
                wards: [],
                stores: [],
                statuses: {
                    'AVAILABLE': 'Available',
                    'RESERVED': 'Reserved',
                    'RUNNING': 'Running',
                },
                form: new Form({
                    id : '',
                    name: '',
                    image_url: '',
                    status: '',
                }),

                fileUploaded: [],
                headers: {},
                fileUploadUrl: '/file/upload'
                // autocompleteItems: [],
            }
        },
        methods: {
            onProvinceChange(province) {
                axios.get("api/districts/list?province_id="+province.id).then(({ data }) => (this.districts = data.data));
            },
            onDistrictChange(district) {
                axios.get("api/wards/list?district_id="+district.id).then(({ data }) => (this.wards = data.data));
            },
            onWardChange(ward) {
                axios.get("api/stores/list?ward_id="+ward.id).then(({ data }) => (this.stores = data.data));
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

                axios.get('api/positions?page=' + page).then(({ data }) => (this.positions = data.data));

                this.$Progress.finish();
            },
            loadPositions(){

                // if(this.$gate.isAdmin()){
                axios.get("api/positions").then(({ data }) => (this.positions = data.data));
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

            editModal(position){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(position);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            createPosition(){
                this.$Progress.start();

                console.log(this.form);
                this.form.post('api/positions')
                    .then((data)=>{
                        if(data.data.success){
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
                                title: 'Some error occured! Please try again'
                            });

                            this.$Progress.failed();
                        }
                    })
                    .catch((error)=>{

                        console.log(error.response.data.errors);

                        Toast.fire({
                            icon: 'error',
                            title: 'Some error occured! Please try again'
                        });
                    })
            },
            updatePosition(){
                this.$Progress.start();
                this.form.put('api/positions/'+this.form.id)
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
            deletePosition(id){
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
                        this.form.delete('api/positions/'+id).then(()=>{
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            // Fire.$emit('AfterCreate');
                            this.loadPositions();
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
