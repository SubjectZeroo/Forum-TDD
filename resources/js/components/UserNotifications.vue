<template>
  <div class="dropdown" v-if="notifications.length">
    <button
      class="btn dropdown-toggle"
      type="button"
      id="dropdownMenuButton1"
      data-bs-toggle="dropdown"
      aria-expanded="false"
    >
      <span class="glyphicon glyphicon-bell"></span>
    </button>
    <ul
      class="dropdown-menu"
      aria-labelledby="dropdownMenuButton1"
      v-for="notification in notifications"
    >
      <li>
        <a
          class="dropdown-item"
          :href="notification.data.link"
          v-text="notification.data.message"
          @click.prevent="markAsRead(notification)"
        >
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return { notifications: false };
  },

  created() {
    axios
      .get("/profiles/" + window.App.user.name + "/notifications")
      .then((response) => (this.notifications = response.data));
  },
  methods: {
    markAsRead(notification) {
      axios.delete(
        "/profiles/" +
          window.App.user.name +
          "/notifications/" +
          notification.id
      );
    },
  },
};
</script>

<style>
</style>
