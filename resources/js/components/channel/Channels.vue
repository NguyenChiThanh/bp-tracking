<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Channel List</h3>

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
                      <th>Image</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="channel in channels.data" :key="channel.id">

                      <td>{{channel.id}}</td>
                      <td>{{channel.name}}</td>
                      <td><img v-bind:src="channel.image_url" class="img-thumbnail img-fluid" width="20%" v-bind:alt="channel.name +' image'"></td>
                      <td>{{channel.status}}</td>
                      <!-- <td><img v-bind:src="'/' + channel.photo" width="100" alt="channel"></td> -->
                      <td>

                        <a href="#" @click="editModal(channel)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteChannel(channel.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="channels" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Create New Channel</h5>
                    <h5 class="modal-title" v-show="editmode">Edit Channel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateChannel() : createChannel()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="file" @change="onFileChange" class="form-control">
                        </div>
                        <div class="form-group" v-if="editmode">
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
    import FileUpload from 'v-file-upload';

    export default {
      components: {
          FileUpload,
        },
        data () {
            return {
                editmode: false,
                channels : {},
                statuses: {
                    'ACTIVE': 'Active',
                    'INACTIVE': 'Inactive',
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

              axios.get('api/channels?page=' + page).then(({ data }) => (this.channels = data.data));

              this.$Progress.finish();
          },
          loadChannels(){

            // if(this.$gate.isAdmin()){
              axios.get("api/channels").then(({ data }) => (this.channels = data.data));
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
          editModal(channel){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(channel);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createChannel(){
              this.$Progress.start();

              console.log(this.form);
              this.form.post('api/channels')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadChannels();

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
          updateChannel(){
              this.$Progress.start();
              this.form.put('api/channels/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadChannels();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteChannel(id){
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
                              this.form.delete('api/channels/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadChannels();
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

            this.loadChannels();
            // this.loadCategories();
            // this.loadTags();

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
