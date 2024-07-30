<template>
  <div>
    <Head :title="titleComputed" />

    <Heading class="mb-6">{{ titleComputed }}</Heading>

    <Card
      class="flex flex-col items-center justify-center"
      style="min-height: 300px"
    >
      <div class="resource-tree">
        <Draggable class="mtl-tree" v-model="data.treeData" treeLine>
          <template #default="{ node, stat }">
            <OpenIcon
              v-if="stat.children.length"
              :open="stat.open"
              class="mtl-mr"
              @click.native="stat.open = !stat.open"
            />
            <span class="mtl-ml">{{ node.text }}</span>
          </template>
        </Draggable>
      </div>
    </Card>
  </div>

  <div class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 md:space-x-3">
    <button @click="save" class="border text-left appearance-none cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 relative disabled:cursor-not-allowed inline-flex items-center justify-center shadow h-9 px-3 bg-primary-500 border-primary-500 hover:[&:not(:disabled)]:bg-primary-400 hover:[&:not(:disabled)]:border-primary-400 text-white dark:text-gray-900">Save</button>
  </div>
</template>

<script setup>
import { onMounted, toRefs, computed, reactive } from "vue"
import { Draggable, OpenIcon } from '@he-tree/vue'

const props = defineProps({
  resource: String,
})

const { resource } = toRefs(props)

const data = reactive({
  treeData: [ ],
})

const titleComputed = computed(() => {
  const r = resource.value;

  return r[0].toUpperCase() + r.slice(1) + ' Tree';
});

async function save() {
  try {
    await Nova.request()
      .post(`/nova-vendor/model-tree/${resource.value}`, {
        tree: data.treeData
      });

    document.location.reload();
  } catch (error) {
    alert(error.response.data.message);
  }
}

onMounted(async () => {
  const response = await Nova.request()
    .get(`/nova-vendor/model-tree/${resource.value}`);

  data.treeData = response.data.tree;
});

</script>

<style>
.resource-tree{
  background-color: white;
  border-radius: 6px;
  padding: 24px;
  width: 100%;
  max-width: 600px;
}
</style>
