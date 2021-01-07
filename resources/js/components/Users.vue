<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="$gate.isMod()">
              <div class="card-header">
                <h3 class="card-title">User List</h3>

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
                      <th>Type</th>
                        <th>Roles</th>

                        <th>Name</th>
                      <th>Email</th>
                      <th>Cell Phone</th>
                      <th>Company</th>
                        <th>Brand</th>
                      <th>Status</th>
                      <th>Email Verified?</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="user in users.data" :key="user.id">

                          <td>{{user.id}}</td>
                          <td>{{user.type}}</td>
                         <td>
                             <ul class="list-group-item" v-for="role in user.roles" :key="role.name">
                                 <li>{{role.name}}</li>
                             </ul>
                         </td>
                          <td>{{user.name}}</td>
                          <td>{{user.email}}</td>
                          <td>{{user.cellphone}}</td>
                          <td><span v-if="user.company">{{user.company.name}}</span></td>
                          <td>
                              <ul class="list-group-item" v-for="brand in user.brands" :key="brand.name">
                                  <li>{{brand.name}}</li>
                              </ul>
                          </td>

                          <td>{{user.status}}</td>
                          <td :inner-html.prop="user.email_verified_at | yesno"></td>
                          <td>{{user.created_at}}</td>

                          <td>

                            <a href="#" @click="editModal(user)">
                                <i class="fa fa-edit blue"></i>
                            </a>
                            /
                            <a href="#" @click="deleteUser(user.id)">
                                <i class="fa fa-trash red"></i>
                            </a>
                          </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" :limit="2" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!$gate.isMod()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New User</h5>
                    <h5 class="modal-title" v-show="editmode">Update Partner User's Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Type:</label>
                            <v-select v-model="form.type" label="name"
                                      :options="userTypes"
                                      @input="onUserTypeChange"
                                      :class="{ 'is-invalid': form.errors.has('type')} "></v-select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Roles:</label>
                            <v-select multiple v-model="form.roles" label="name" :options="roles.data"
                                      :class="{ 'is-invalid': form.errors.has('roles')} "></v-select>
                            <has-error :form="form" field="roles"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="email" name="email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Cellphone</label>
                            <input v-model="form.cellphone" type="text" name="cellphone"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('cellphone') }">
                            <has-error :form="form" field="cellphone"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Company:</label>
                            <v-select v-model="form.company" label="name"
                                      :options="company.data"
                                      @input="onCompanyChange"></v-select>
                        </div>
                        <div class="form-group">
                            <label>Brands:</label>
                            <v-select multiple v-model="form.brands" label="name" :options="brands.data"
                                      :class="{ 'is-invalid': form.errors.has('brands')} "></v-select>
                            <has-error :form="form" field="brands"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input v-model="form.password" type="password" name="password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" autocomplete="false">
                            <has-error :form="form" field="password"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" name="password"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" autocomplete="false">
                            <has-error :form="form" field="password_confirmation"></has-error>
                        </div>


                        <div class="form-group">
                            <label>Status</label>
                            <select name="type" v-model="form.status" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('status') }">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <has-error :form="form" field="status"></has-error>
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
    import FileUpload from "v-file-upload";
    import vSelect from "vue-select";
    import {VueGoodTable} from "vue-good-table";

    export default {
        components: {
            vSelect,
        },
        data () {
            return {
                editmode: false,
                users : {},
                roles: {},
                company: {},
                brands: {},
                userTypes: {},
                form: new Form({
                    id : '',
                    type : '',
                    name: '',
                    roles: '',
                    status : '',
                    email: '',
                    cellphone: '',
                    password: '',
                    password_confirmation: '',
                    email_verified_at: '',
                    company: '',
                    brands: '',
                })
            }
        },
        methods: {

            loadUserTypes() {
                this.userTypes = [];
                if(this.$gate.isMod()) {
                    this.userTypes = ['partner_user'];
                }
                if(this.$gate.isAdmin()) {
                    this.userTypes.push('pmc_user');
                }
            },

            onUserTypeChange(userType) {
                this.roles = {};
                axios.get("api/roles?user_type="+userType).then((response) => {
                    console.log(response.data);
                    this.roles = response.data
                })
            },

            loadRoles() {
                if(this.$gate.isAdmin()) {
                    axios.get("api/roles").then((data) => {
                        console.log(data.data)
                        this.roles = data.data;
                    })
                }
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

            onCompanyChange(company) {
                this.brands = {};
                this.form.brands = [];
                axios.get("api/brands/list?company_id="+company.id).then((response) => {
                    this.brands = response.data
                })
            },

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('api/user?page=' + page).then(({ data }) => (this.users = data.data));

                  this.$Progress.finish();
            },
            updateUser(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/user/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.loadUsers();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
                this.onUserTypeChange(user.type);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteUser(id){
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
                                this.form.delete('api/user/'+id).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.loadUsers();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },
          loadUsers(){
            this.$Progress.start();

            if(this.$gate.isMod()){
                axios.get("api/user").then(({ data }) => (this.users = data.data));
            }

            this.$Progress.finish();
          },

          createUser(){
              console.log(this.form);
              // todo hard code for partner user
              this.form.post('api/user').then((response)=> {
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: response.data.message
                  });

                  this.$Progress.finish();
                  this.loadUsers();

              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });
              })
          }

        },
        created() {
            this.$Progress.start();
            this.loadUsers();
            this.loadCompany();
            // this.loadBrands();
            this.loadRoles();
            this.loadUserTypes();
            this.$Progress.finish();
        }
    }
</script>
