<template>
  <!-- <img alt="Vue logo" src="./assets/logo.png" /> -->
  <div class="loader" v-if="isLoading"></div>
  <ApiGateway
    :class="{ loading: isLoading }"
    msg="The following Apis are available"
    :routes="routes"
  />
</template>

<script>
import ApiGateway from "./components/ApiGateway.vue";
import axios from "axios";
export default {
  name: "App",
  components: {
    ApiGateway,
  },
  data() {
    return {
      routes: [],
      isLoading: true,
    };
  },
  mounted() {
    this.getRoutes().then((data) => {
      this.routes = Object.entries(data.endpoints).map((endpoint) => ({
        id: endpoint[0],
        ...endpoint,
      }));
      console.log(this.routes, "a");
    });
  },
  methods: {
    async getRoutes() {
      try {
        const response = await axios.get(
          "http://localhost/event-analytics/public/api/list"
        );
        this.isLoading = false;
        return response.data;
      } catch (error) {
        console.error("Error fetching routes:", error);
        return [];
      }
    },
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

.loader {
  width: 48px;
  height: 48px;
  border: 5px solid #fff;
  border-bottom-color: #42b983;
  border-radius: 50%;
  display: inline-block;
  box-sizing: border-box;
  animation: rotation 1s linear infinite;
}

.loading {
  /* opacity: 0.5; */
  display: none;
  /* pointer-events: none; */
}

@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
