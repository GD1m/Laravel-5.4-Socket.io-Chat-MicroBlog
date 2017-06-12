<template>
    <ul class="nav nav-pills">
        <li v-for="menu_item in menu"
            v-bind:class="{active: currentAttachmentComponent == menu_item.component}">
            <a href="#" v-on:click.prevent="setCurrentAttachmentComponent(menu_item.component)">{{ menu_item.title
                }}</a>
        </li>
    </ul>
</template>
<style>

</style>
<script>
    export default{
        data(){
            return {
                menu: [
                    {
                        component: 'attach-image',
                        title: 'Images',
                    },
                    {
                        component: 'attach-link',
                        title: 'Links',
                    },
                    {
                        component: 'attach-youtube',
                        title: 'YouTube Videos',
                    },
                ]
            }
        },
        props: [
            'currentAttachmentComponent'
        ],
        components: {},
        methods: {
            setCurrentAttachmentComponent: function (component) {
                this.$emit('update:currentAttachmentComponent', component)
            }
        }
    }
</script>