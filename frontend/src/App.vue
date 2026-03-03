<template>
  <router-view />
</template>

<script>
export default {
  name: 'App'
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

:root {
  --primary: #22c55e;
  --primary-soft: #ecfdf5;
  --sidebar-bg: #fff;
  --bg-main: #f8fafc;
  --text-main: #1e293b;
  --text-muted: #64748b;
  --border-light: #f1f5f9;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Outfit', sans-serif;
  color: var(--text-main);
  background: var(--bg-main);
  overflow-x: hidden;
}
</style>
