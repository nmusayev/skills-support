import VueRouter from 'vue-router';

// 1. Importing route components.
import Profile from "./components/Profile";
import Community from "./components/Community";
import QuestionDetail from "./components/QuestionDetail";
import Home from "./components/Home";
import SearchResults from "./components/SearchResults";
import Users from "./components/Users";

// 2. Define some routes
const routes = [
    { path: '/profile', component: Profile },
    { path: '/profile/:id', component: Profile, name: 'profile' },
    { path: '/community', component: Community },
    { path: '/users', component: Users },
    { path: '/question-detail/:id', component: QuestionDetail },
    { path: '/search-results/:key', component: SearchResults },
    { path: '*', component: Home },
];

// 3. Create the router instance and pass the `routes` option
const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
});

export default router;
