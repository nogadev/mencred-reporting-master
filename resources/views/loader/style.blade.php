<style>
    .preloader {
    width: 70px;
    height: 70px;
    border: 10px solid #eee;
    border-top: 10px solid #666;
    border-radius: 50%;
    animation-name: girar;
    animation-duration: 2s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
    position: fixed;
    left: 50%;
    top: 50%;
    z-index: 1;
    }
    @keyframes girar {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
    }
</style>