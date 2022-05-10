<template>
  <div :id="'reply-' + id" class="card mt-2" v-if="show">
    <div class="card-header" :class="isBest ? 'bg-success' : ''">
      <div class="d-flex justify-content-between align-items-center">
        <h5>
          <a :href="'/profiles/' + reply.owner.name" v-text="reply.owner.name">
          </a>
          said {{ new Date(reply.created_at).toLocaleDateString() }}...
        </h5>
        <div v-if="signedIn">
          <favorite :reply="reply"></favorite>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div v-if="editing">
        <form @submit="update">
          <div class="form-group">
            <textarea class="form-control" v-model="body" required></textarea>
          </div>
          <button class="btn btn-sm btn-primary">Update</button>
          <button
            class="btn btn-sm btn-link"
            @click="editing = false"
            type="button"
          >
            Cancel
          </button>
        </form>
      </div>
      <div v-else v-html="body"></div>
    </div>

    <div
      class="card-footer d-flex"
      v-if="authorize('owns', reply) || authorize('owns', reply.thread)"
    >
      <div v-if="authorize('owns', reply)">
        <button class="btn btn-primary btn-sm mr-1" @click="editing = true">
          Edit
        </button>
        <button class="btn btn-danger btn-sm" @click="destroy">Delete</button>
      </div>

      <button
        class="btn btn-sm btn-default"
        @click="markBestReply"
        v-show="!isBest"
        v-if="authorize('owns', reply.thread)"
      >
        Best Reply
      </button>
    </div>
  </div>
</template>

<script>
import Favorite from "./Favorite.vue";
export default {
  props: ["reply"],

  components: { Favorite },
  data() {
    return {
      show: true,
      editing: false,
      id: this.reply.id,
      body: this.reply.body,
      isBest: this.reply.isBest,
      //reply: this.data
    };
  },

  computed: {
    // isBest() {
    //     return window.thread.best_reply_id == this.id;
    // }
    // ago() {
    //     return moment(this.data.created_at).fromNow() + '....'
    // }
    // signedIn() {
    //     return window.App.signedIn;
    // },
    // canUpdate() {
    //    return this.authorize(user => this.data.user_id == user.id);
    // //   return  this.data.user_id == window.App.user.id;
    // }
  },

  created() {
    window.events.$on("best-reply-selected", (id) => {
      this.isBest = id === this.id;
    });
  },
  methods: {
    update() {
      axios
        .patch("/replies/" + this.id, {
          body: this.body,
        })
        .catch((error) => {
          flash(error.response.data, "danger");
        });

      this.editing = false;
      flash("Update!");
    },
    destroy() {
      axios.delete("/replies/" + this.id);

      this.$emit("deleted", this.id);
    },

    hide() {
      setTimeout(() => {
        this.show = false;
      }, 1000);
    },
    markBestReply() {
      axios.post("/replies/" + this.id + "/best");

      window.events.$emit("best-reply-selected", this.id);
    },
  },
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
