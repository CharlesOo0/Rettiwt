var statData = document.getElementById('statData');

if (statData != null) {
    var postsPerWeek = JSON.parse(statData.dataset.posts_per_week);
    var postsPerMonth = JSON.parse(statData.dataset.posts_per_month);

    var likePerWeek = JSON.parse(statData.dataset.like_per_week);
    var likePerMonth = JSON.parse(statData.dataset.like_per_month);

    var likeReceivedPerWeek = JSON.parse(statData.dataset.like_received_per_week);
    var likeReceivedPerMonth = JSON.parse(statData.dataset.like_received_per_month);

    var followPerWeek = JSON.parse(statData.dataset.follow_per_week);
    var followPerMonth = JSON.parse(statData.dataset.follow_per_month);

    var followerPerWeek = JSON.parse(statData.dataset.follower_per_week);
    var followerPerMonth = JSON.parse(statData.dataset.follower_per_month);

    //-------------------- Posts par semaine
    var labels = [];
    var data = [];

    if (postsPerWeek !== null) {  // Si on a des données
        labels = postsPerWeek.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = postsPerWeek.map(function (data) {  // On récupère le nombre de posts
            return data.count;
        });
    }

    var ctx = document.getElementById('postWeekChart').getContext('2d'); // On récupère le canvas
    var postChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Post par semaines.',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });    

    //-------------------- Posts par mois

    labels = [];
    data = [];

    if (postsPerMonth !== null) { // Si on a des données
        labels = postsPerMonth.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = postsPerMonth.map(function (data) { // On récupère le nombre de posts
            return data.count;
        });
    }

    var ctx = document.getElementById('postMonthChart').getContext('2d'); // On récupère le canvas
    var postMonthChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Posts par mois.',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Likes par semaine

    labels = [];
    data = [];

    if (likePerWeek !== null) { // Si on a des données 
        labels = likePerWeek.map(function (data) { // On récupère les dates 
            return data.date;
        });
        data = likePerWeek.map(function (data) { // On récupère le nombre de likes
            return data.count;
        });
    }

    var ctx = document.getElementById('likeWeekChart').getContext('2d'); // On récupère le canvas
    var likeChart = new Chart(ctx, {  // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Likes mit par semaines.',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Likes par mois

    labels = [];
    data = [];

    if (likePerMonth !== null) { // Si on a des données
        labels = likePerMonth.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = likePerMonth.map(function (data) {  // On récupère le nombre de likes
            return data.count;
        });
    }

    var ctx = document.getElementById('likeMonthChart').getContext('2d'); // On récupère le canvas
    var likeMonthChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Likes mit par mois.',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Likes reçus par semaine

    labels = [];
    data = [];

    if (likeReceivedPerWeek !== null) { // Si on a des données
        labels = likeReceivedPerWeek.map(function (data) { // On récupère les dates 
            return data.date;
        });
        data = likeReceivedPerWeek.map(function (data) { // On récupère le nombre de likes
            return data.count;
        });
    }

    var ctx = document.getElementById('likeReceivedWeekChart').getContext('2d'); // On récupère le canvas
    var likeReceivedChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Likes reçus par semaines.',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Likes reçus par mois

    labels = [];
    data = [];

    if (likeReceivedPerMonth !== null) { // Si on a des données
        labels = likeReceivedPerMonth.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = likeReceivedPerMonth.map(function (data) { // On récupère le nombre de likes
            return data.count;
        });
    }

    var ctx = document.getElementById('likeReceivedMonthChart').getContext('2d'); // On récupère le canvas
    var likeReceivedMonthChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Likes reçus par mois.',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Follow par semaine

    labels = [];
    data = [];

    if (followPerWeek !== null) { // Si on a des données
        labels = followPerWeek.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = followPerWeek.map(function (data) { // On récupère le nombre de follow
            return data.count;
        });
    }

    var ctx = document.getElementById('followWeekChart').getContext('2d'); // On récupère le canvas
    var followChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Follow par semaines.',
                data: data,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Follow par mois

    labels = [];
    data = [];

    if (followPerMonth !== null) { // Si on a des données
        labels = followPerMonth.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = followPerMonth.map(function (data) { // On récupère le nombre de follow
            return data.count;
        });
    }

    var ctx = document.getElementById('followMonthChart').getContext('2d'); // On récupère le canvas
    var followMonthChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Follow par mois.',
                data: data,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Follower par semaine

    labels = [];
    data = [];

    if (followerPerWeek !== null) { // Si on a des données
        labels = followerPerWeek.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = followerPerWeek.map(function (data) { // On récupère le nombre de follower
            return data.count;
        });
    }

    var ctx = document.getElementById('followerWeekChart').getContext('2d'); // On récupère le canvas
    var followerChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Follower par semaines.',
                data: data,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        }
    });

    //-------------------- Follower par mois

    labels = [];
    data = [];

    if (followerPerMonth !== null) { // Si on a des données
        labels = followerPerMonth.map(function (data) { // On récupère les dates
            return data.date;
        });
        data = followerPerMonth.map(function (data) { // On récupère le nombre de follower
            return data.count;
        });
    }

    var ctx = document.getElementById('followerMonthChart').getContext('2d'); // On récupère le canvas
    var followerMonthChart = new Chart(ctx, { // On crée le graphique
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Follower par mois.',
                data: data,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        }
    });

    
}

