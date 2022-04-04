window.onload = function () {
    const project = document.getElementById('project-data');
    const projectForecast = document.getElementById('project-forecast');

    if (project && projectForecast) {
        const projectData = JSON.parse(project.dataset.project);
        console.log(projectData);

        const config = {
            binaryThresh: 0.5,
            hiddenLayers: [10], // array of ints for the sizes of the hidden layers in the network
            activation: 'sigmoid', // supported activation types: ['sigmoid', 'relu', 'leaky-relu', 'tanh'],
            leakyReluAlpha: 0.01, // supported for activation type 'leaky-relu'
        };

        const trainingData = [
            {
                input: {
                    initialAmount: 5000,
                    leftAmount: 5000,
                    archived: 0,
                    daysLeft: 320,
                    dangerRisks: 0,
                    highRisks: 0,
                    mediumRisks: 0,
                    lowRisks: 1,
                    canBeIgnoredRisks: 2
                },
                output: {
                    '😇': 1
                }
            },
            {
                input: {
                    initialAmount: 5000,
                    leftAmount: 3000,
                    archived: 0,
                    daysLeft: null,
                    dangerRisks: 0,
                    highRisks: 0,
                    mediumRisks: 0,
                    lowRisks: 3,
                    canBeIgnoredRisks: 1
                },
                output: {
                    '😀': 1
                }
            },
            {
                input: {
                    initialAmount: 2000,
                    leftAmount: 100,
                    archived: 0,
                    daysLeft: 30,
                    dangerRisks: 0,
                    highRisks: 0,
                    mediumRisks: 1,
                    lowRisks: 3,
                    canBeIgnoredRisks: 0
                },
                output: {
                    '😟': 1
                }
            },
            {
                input: {
                    initialAmount: 5000,
                    leftAmount: -2000,
                    archived: 0,
                    daysLeft: 20,
                    dangerRisks: 0,
                    highRisks: 2,
                    mediumRisks: 1,
                    lowRisks: 0,
                    canBeIgnoredRisks: 0
                },
                output: {
                    '😰': 1
                }
            }
        ];

        const net = new brain.NeuralNetwork(config);

        net.train(trainingData);

        const output = net.run(projectData);
        console.log(output);
        const highestValue = Object.keys(output).reduce(function (previous, key) {
            return previous < output[key] ? output[key] : previous;
        }, 0)
        const forecast = Object.keys(output).find(key => output[key] === highestValue);

        // delete everything inside the project forecast div and add a span with the current forecast
        while (projectForecast.firstChild) {
            projectForecast.removeChild(projectForecast.firstChild);
        }
        const span = document.createElement('span');
        span.innerText = forecast;
        projectForecast.appendChild(span);
    }
}
