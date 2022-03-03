<template>
        <div :id="'reply-'+id" class="card mt-2" v-if="show">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>
                        <a :href="'/profiles/'+data.owner.name"
                            v-text="data.owner.name">
                        </a> said {{ data.created_at }}...
                    </h5>
                        <div v-if="signedIn">
                            <favorite :reply="data"></favorite>
                        </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body"></textarea>
                    </div>
                    <button class="btn btn-sm btn-primary" @click="update">Update</button>
                    <button class="btn btn-sm btn-link" @click="editing = false">Cancel</button>
                </div>
                <div v-else v-text="body">

                </div>
            </div>

                <div class="card-footer d-flex" v-if="canUpdate">
                    <button class="btn btn-primary btn-sm mr-1" @click="editing = true">Edit</button>
                    <button class="btn btn-danger btn-sm" @click="destroy">Delete</button>

                </div>

        </div>
</template>

<script>
import Favorite from './Favorite.vue';
export default {
    props: ['data'],

    components: { Favorite },
    data() {
        return {
            show: true,
            editing: false,
            id: this.data.id,
            body: this.data.body
        };
    },

    computed: {
        signedIn() {
            return window.App.signedIn;
        },
        canUpdate() {
           return this.authorize(user => this.data.user_id == user.id);
        //   return  this.data.user_id == window.App.user.id;
        }
    },
    methods: {
        update() {
            axios.patch('/replies/' + this.data.id, {
                body: this.body
            });

            this.editing = false;
            flash('Update!');
        },
        destroy() {
            axios.delete('/replies/' + this.data.id);

            this.$emit('deleted', this.data.id);

        },

        hide() {
            setTimeout(() => {
                    this.show = false;
            }, 1000);
        }
    }
}
</script>

<style>
    .fade-enter-active, .fade-leave-active {
    transition: opacity .5s
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0
    }
</style>
