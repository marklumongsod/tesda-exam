import "./chunk-G3PMV62Z.js";

// node_modules/.pnpm/laravel-vite-plugin@1.2.0_vite@6.0.11/node_modules/laravel-vite-plugin/inertia-helpers/index.js
async function resolvePageComponent(path, pages) {
  for (const p of Array.isArray(path) ? path : [path]) {
    const page = pages[p];
    if (typeof page === "undefined") {
      continue;
    }
    return typeof page === "function" ? page() : page;
  }
  throw new Error(`Page not found: ${path}`);
}
export {
  resolvePageComponent
};
//# sourceMappingURL=laravel-vite-plugin_inertia-helpers.js.map
