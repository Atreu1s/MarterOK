<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const { echo } = defineProps({ echo: { type: Object, required: true } });
const article = ref(null);

onMounted(() => {
    echo.channel('articles').listen('NewArticleEvent', (event) => {
        article.value = event.article;
    });
});

onUnmounted(() => echo.disconnect());
</script>

<template>
    <div v-if="article" role="status">
        Опубликована новая статья <a :href="article.url">{{ article.title }}</a>.
        <button type="button" @click="article = null">Закрыть</button>
    </div>
</template>
