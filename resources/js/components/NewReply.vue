<template>
  <div v-if="signedIn">
    <div class="card mt-2">
      <div class="card-body">
        <div class="form-group">
          <textarea
            name="body"
            id="body"
            class="form-control"
            placeholder="Have something to say?"
            rows="5"
            v-model="body"
          >
          </textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-2" @click="addReply">
          Post
        </button>
      </div>
    </div>
  </div>
  <p v-else>
    Please <a href="/login">sign in</a> to participate in this discussion.
  </p>
</template>

<script>
import Tribute from "tributejs";
export default {
  data() {
    return {
      body: "",
    };
  },

  // computed: {
  //     signedIn() {
  //         return window.App.signedIn;
  //     }
  // },

  mounted() {
    let tribute = new Tribute({
      //column to search against in the object (accepts function or strign)
      lookup: "value",

      fillAttr: "value",

      values: function (query, cb) {
        axios
          .get("/api/users", { params: { name: query } })
          .then(function (response) {
            console.log(response);
            cb(response.data);
          });
      },
    });
    tribute.attach(document.querySelectorAll("#body"));
  },

  methods: {
    addReply() {
      axios
        .post(location.pathname + "/replies", { body: this.body })
        .catch((error) => {
          flash(error.response.data, "danger");
        })
        .then(({ data }) => {
          this.body = "";
          flash("Your reply has been posted.");
          this.$ref.trix.$ref.trix.valuue = "";
          this.$emit("created", data);
        });
    },
  },
};
</script>

<style>
</style>
