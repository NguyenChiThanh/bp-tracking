<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Store List</h3>

<!--                <div class="card-tools">-->

<!--                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">-->
<!--                      <i class="fa fa-plus-square"></i>-->
<!--                      Add New-->
<!--                  </button>-->
<!--                </div>-->
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Code</th>
                      <th>Desc</th>
                      <th>Status</th>
                      <th>Level</th>
                      <th>Address</th>
                      <th>Ward</th>
                      <th>District</th>
                      <th>Province</th>
                      <th>Images</th>
<!--                      <th>Action</th>-->
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="store in stores.data" :key="store.id">

                      <td>{{store.id}}</td>
                      <td>{{store.name}}</td>
                      <td>{{store.code}}</td>
                      <td>{{store.description}}</td>
                      <td>{{store.status}}</td>
                      <td>{{store.level}}</td>
                      <td>{{store.address}}</td>
                      <td>{{store.ward}}</td>
                      <td>{{store.district}}</td>
                      <td>{{store.province}}</td>
                      <td>{{store.images}}</td>
                      <!-- <td><img v-bind:src="'/' + store.photo" width="100" alt="store"></td> -->
<!--                      <td>-->

<!--                        <a href="#" @click="editModal(store)">-->
<!--                            <i class="fa fa-edit blue"></i>-->
<!--                        </a>-->
<!--                        /-->
<!--                        <a href="#" @click="deleteStore(store.id)">-->
<!--                            <i class="fa fa-trash red"></i>-->
<!--                        </a>-->
<!--                      </td>-->
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="stores" :limit="2" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Create New Store</h5>
                    <h5 class="modal-title" v-show="editmode">Edit Store</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateStore() : createStore()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input v-model="form.description" type="text" name="description"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                            <has-error :form="form" field="description"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" v-model="form.status" :class="{ 'is-invalid': form.errors.has('status') }">
                                <option
                                    v-for="(status, index) in statuses" :key="index"
                                    :value="index"
                                    :selected="index == form.status">{{ status }}</option>
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
    // import VueTagsInput from '@johmun/vue-tags-input';

    export default {
      // components: {
      //     VueTagsInput,
      //   },
        data () {
            return {
                editmode: false,
                stores : {},
                statuses: {
                    'IN_OPERATING': 'In Operating',
                    'COMING_SOON': 'Coming soon',
                    'CLOSED': 'Closed',
                },
                form: new Form({
                    id : '',
                    name: '',
                    description: '',
                    status: '',
                }),

                // autocompleteItems: [],
            }
        },
        methods: {

          getResults(page = 1) {

              this.$Progress.start();

              axios.get('api/stores?page=' + page).then(({ data }) => (this.stores = data.data));

              this.$Progress.finish();
          },
          loadStores(){

            // if(this.$gate.isAdmin()){
              axios.get("api/stores").then(({ data }) => (this.stores = data.data));
            // }
          },
          loadCategories(){
              axios.get("/api/category/list").then(({ data }) => (this.statuses = data.data));
          },
          // loadTags(){
          //     axios.get("/api/tag/list").then(response => {
          //         this.autocompleteItems = response.data.data.map(a => {
          //             return { text: a.name, id: a.id };
          //         });
          //     }).catch(() => console.warn('Oh. Something went wrong'));
          // },
          editModal(store){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(store);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createStore(){
              this.$Progress.start();

              this.form.post('api/stores')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadStores();

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
          updateStore(){
              this.$Progress.start();
              this.form.put('api/stores/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadStores();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteStore(id){
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
                              this.form.delete('api/stores/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadStores();
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

            this.loadStores();
            // this.loadCategories();
            // this.loadTags();

            this.$Progress.finish();
        },
        filters: {
            truncate: function (text, length, suffix) {
                return text.substring(0, length) + suffix;
            },
        }
    }
</script>
