<template>
    <div v-if="signedIn">
        <div class="card mt-2">
            <div class="card-body">
                <div class="form-group">
                    <textarea name="body"
                                id="body"
                                class="form-control"
                                placeholder="Have something to say?"
                                rows="5"
                                required
                                v-model="body">
                    </textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2" @click="addReply">Post</button>
            </div>
        </div>
    </div>
    <p v-else> Please <a href="/login">sign in</a> to participate in this discussion. </p>

</template>

<script>
export default {
    data() {
        return {
            body: '',
        }
    },

    computed: {
        signedIn() {
            return window.App.signedIn;
        }
    },

    methods: {
        addReply() {
            axios.post(location.pathname + '/replies', {body: this.body})
                .catch(error => {
                    flash(error.response.data, 'danger');
                })
                .then(({data}) => {
                    this.body = '';
                    flash('Your reply has been posted.');
                    this.$emit('created', data);
                });
        }
    }
}
</script>

<style>

</style>
