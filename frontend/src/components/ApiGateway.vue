<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <div v-if="processedRoutes.length > 0" class="route-container">
      <v-card
        class="route-card"
        v-for="route in processedRoutes"
        :key="route.id"
        @click="openRoute(route.url)"
      >
        <v-card-title class="route-card-title">
          <a :href="route.url">{{ route.name }}</a>
        </v-card-title>
        <p>
          <small>{{ route.url }}</small>
        </p>
      </v-card>
    </div>
    <div v-else>
      <p>No routes found</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "ApiGateway",
  props: {
    msg: String,
    routes: Array,
  },
  computed: {
    processedRoutes() {
      return this.routes.map((route) => ({
        id: route[0],
        url: route[0],
        name: route[1],
      }));
    },
  },
  methods: {
    openRoute(url) {
      window.location.href = url;
    },
  },
};
</script>

<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
  text-decoration: none;
}
.route-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 20px;
  margin: 0 auto;
  width: 100%;
  max-width: 1200px;
  justify-content: center;
}
.route-card {
  padding: 2rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: calc(33.33% - 90px);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  cursor: pointer;
  font-size: 1.2rem;
  font-weight: bold;
  background-color: #141414;
  border: 3px solid transparent;
  color: #fff;
}
.route-card.route-card-title {
  width: 70px;
}
.route-card:hover {
  background-color: #202020;
  transition: all 0.3s ease;
  border: 3px solid rgb(5, 135, 5);
}
</style>
