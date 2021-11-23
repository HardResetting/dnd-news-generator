<template>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">DnD-News-Generator</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <router-link class="nav-link" to="/">Home</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" to="/Items">Items</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" to="/Templates">Templates</router-link>
                        </li>                        
                        <li class="nav-item">
                            <router-link class="nav-link" to="/Templates/Compile">Compile random Templates</router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <router-view />
    </div>
</template>