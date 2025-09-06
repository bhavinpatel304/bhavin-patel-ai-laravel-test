<template>
  <div class="layout">
    <aside class="layout__sidebar">
      <h2 class="layout__sidebar__logo">Admin Panel</h2>
      <nav class="layout__sidebar__nav">
        <ul>            
          <li><router-link to="/">Dashboard</router-link></li>
          <li><router-link to="/tickets">Tickets</router-link></li>
        </ul>
      </nav>
    </aside>

    <main class="layout__content">
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
export default {
  name: "DefaultLayout",
};
</script>

<style lang="scss" scoped>
.layout {
  display: flex;
  min-height: 100vh;
  font-family: sans-serif;

  /******
  sidebar
  ******/
  &__sidebar 
  {
    width: 20%;
    background-color: #2c3e50;
    color: white;
    padding: 20px;

    &__logo {
      margin-bottom: 30px;
      font-size: 1.5rem;
    }

    &__nav {
      ul {
        list-style: none;
        padding: 0;
      }

      li {
        margin-bottom: 15px;
      }

      a {
        color: white;
        text-decoration: none;

        &.router-link-active {
          font-weight: bold;
          text-decoration: underline;
        }
      }
    }
  }

  /******
  sidebar
  ******/
  &__content {
    width: 80%;
    padding: 20px;
    background-color: #ecf0f1;
  }
}

</style>
