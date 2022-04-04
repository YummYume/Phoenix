window.onload = function () {
    const project = document.getElementById('project-data');
    const projectForecast = document.getElementById('project-forecast');

    if (project && projectForecast) {
        const projectData = JSON.parse(project.dataset.project);
        console.log(projectData);

        const config = {
            binaryThresh: 0.5,
            hiddenLayers: [10, 10, 10], // array of ints for the sizes of the hidden layers in the network
            activation: 'sigmoid', // supported activation types: ['sigmoid', 'relu', 'leaky-relu', 'tanh'],
            leakyReluAlpha: 0.01, // supported for activation type 'leaky-relu'
        };

        const trainingData = [
            {
                input: {
                    budgetPercentage: 0.1,
                    daysPercentage: 0.5,
                    riskPercentage: 1,
                    milestonePercentage: 0.8
                },
                output: {
                    '😇': 1
                }
            },
            {
                input: {
                    budgetPercentage: 0.25,
                    daysPercentage: 0.3,
                    riskPercentage: 0.8,
                    milestonePercentage: 0.75
                },
                output: {
                    '😀': 1
                }
            },
            {
                input: {
                    budgetPercentage: 0.35,
                    daysPercentage: 0.1,
                    riskPercentage: 0.9,
                    milestonePercentage: 0.4
                },
                output: {
                    '😟': 1
                }
            },
            {
                input: {
                    budgetPercentage: 1,
                    daysPercentage: 0.9,
                    riskPercentage: 0.75,
                    milestonePercentage: 0.2
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

        const diagram = document.getElementById('brain-diagram');

        if (diagram) {
            diagram.innerHTML = brain.utilities.toSVG(net.toJSON());
        }
    }
}
