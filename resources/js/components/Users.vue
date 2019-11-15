<template>
    <div class="container">
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Responsive Hover Table</h3>

                        <div class="card-tools">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary input-group-append" data-toggle="modal" @click="addUserModal">
                                Add New User
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
                                <th>Email</th>
                                <th>Registered At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users" v-bind:key="user.id">
                                <td>{{user.id}}</td>
                                <td>{{user.name | capitalize}}</td>
                                <td>{{user.email | capitalize}}</td>
                                <td>{{user.created_at}}</td>
                                <td>
                                    <a href="#" @click="editUserModal(user)">
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
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editMode" id="exampleModalLabel">Add New User</h5>
                        <h5 class="modal-title" v-show="editMode" id="exampleModalLabel">Update Existing User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode ? updateUser() : createUser()">
                    <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input v-model="form.email" type="text" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="form.password" type="password" name="password"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <option value="">Select User Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">Standard User</option>
                                <option value="author">Author</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Bio</label>
                            <input v-model="form.bio" type="text" name="bio"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Photo</label>
                            <input v-model="form.photo" type="text" name="photo"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('photo') }">
                            <has-error :form="form" field="photo"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" v-show="!editMode" class="btn btn-primary">Save changes</button>
                        <button type="submit" v-show="editMode" class="btn btn-primary">Update</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                editMode: false,
                users: {},
                // Create a new form instance
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                    created_at: ''
                })
            }
        },
        methods:{
            addUserModal() {
                this.editMode = false;
                this.form.reset();
                $('#exampleModal').modal('show');
            },
            editUserModal(user) {
                this.editMode = true;
                this.form.reset();
                $('#exampleModal').modal('show');
                this.form.fill(user);
            },
            updateUser(id) {
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id)
                .then(() => {
                    $('#exampleModal').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'User Updated successfully'
                    });

                    Fire.$emit('AfterUserCreate');
                })
                    .catch(() => {

                    })
            },
            deleteUser(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    this.form.delete('api/user/'+id).then(() => {
                        if (result.value) {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            Fire.$emit('AfterUserCreate');
                        }
                    }).catch(() => {
                        Swal.fire(
                            'Failed!',
                            'Something went wrong.',
                            'warning'
                        )
                    });

                })
            },
            loadUsers() {
                axios.get('api/user').then(({data}) => this.users = data.data)
            },
            createUser() {
                // this.editMode = false;
                this.$Progress.start();
                this.form.post('api/user')
                    .then(({ data }) => { console.log(data) })
                    .then(() => {
                        $('#exampleModal').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: 'User Created successfully'
                        });

                        // this.loadUsers();
                        Fire.$emit('AfterUserCreate');

                        this.$Progress.finish();
                    })
                    .catch(() => {

                    });




            }
        },
        created() {
            this.loadUsers();
            // setInterval(() => this.loadUsers(), 3000);
            Fire.$on('AfterUserCreate', () => {
                this.loadUsers();
            });
        },
        filters: {
            capitalize: function (value) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        }
    }
</script>
