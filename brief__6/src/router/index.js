import {createRouter,createWebHistory} from 'vue-router'
import Dashbord from '../views/dashbord.vue'
import acceuil from "@/views/acceuil";

const routes=[
    {
        path:'/',
        name: 'acceuil',
        component:acceuil,
    },
    {
        path: '/Dashbord',
        name: 'Dashbord',
        component:Dashbord,
    },

]
const router=createRouter({
    routes: routes,
    history: createWebHistory()

})
export default router