// on page loaded
window.onload = function () {
    const project = document.getElementById('project-data');
    const projectForecast = document.getElementById('project-forecast');

    if (project && projectForecast) {
        const projectData = JSON.parse(project.dataset.project);
        console.log(projectData);

        const config = {
            binaryThresh: 0.5,
            hiddenLayers: [5], // array of ints for the sizes of the hidden layers in the network
            activation: 'sigmoid', // supported activation types: ['sigmoid', 'relu', 'leaky-relu', 'tanh'],
            leakyReluAlpha: 0.01, // supported for activation type 'leaky-relu'
        };

        const trainingData = [
            {
                input: {
                    now: Date.now(),
                    budget: {
                        initialAmount: 5000,
                        leftAmount: 4000,
                        spentAmount: 1000,
                    },
                    project: {
                        archived: false,
                        endAt: null,
                        startAt: {
                            date: "2021-05-16 22:01:06.000000",
                            timezone: "UTC",
                            timezone_type: 3,
                        },
                    },
                    risks: [
                        {
                            probability: "very_low",
                            resolvedAt: null,
                            identifiedAt: {
                                date: "2022-05-16 22:01:06.000000",
                                timezone: "UTC",
                                timezone_type: 3,
                            },
                            severity: "very_high",
                        }
                    ]
                },
                output: {
                    '😇': 1,
                    '😀': 0,
                    '😟': 0,
                    '😰': 0,
                }
            },
            {
                input: {
                    now: Date.now(),
                    budget: {
                        initialAmount: 5000,
                        leftAmount: 3000,
                        spentAmount: 2000,
                    },
                    project: {
                        archived: false,
                        endAt: null,
                        startAt: {
                            date: "2021-10-16 22:01:06.000000",
                            timezone: "UTC",
                            timezone_type: 3,
                        },
                    },
                    risks: [
                        {
                            probability: "medium",
                            resolvedAt: null,
                            identifiedAt: {
                                date: "2022-05-16 22:01:06.000000",
                                timezone: "UTC",
                                timezone_type: 3,
                            },
                            severity: "very_high",
                        }
                    ]
                },
                output: {
                    '😇': 0,
                    '😀': 1,
                    '😟': 0,
                    '😰': 0,
                }
            },
            {
                input: {
                    now: Date.now(),
                    budget: {
                        initialAmount: 5000,
                        leftAmount: 4000,
                        spentAmount: 1000,
                    },
                    project: {
                        archived: false,
                        endAt: {
                            date: "2022-04-16 22:01:06.000000",
                            timezone: "UTC",
                            timezone_type: 3,
                        },
                        startAt: {
                            date: "2021-05-16 22:01:06.000000",
                            timezone: "UTC",
                            timezone_type: 3,
                        },
                    },
                    risks: [
                        {
                            probability: "very_high",
                            resolvedAt: null,
                            identifiedAt: {
                                date: "2022-05-16 22:01:06.000000",
                                timezone: "UTC",
                                timezone_type: 3,
                            },
                            severity: "medium",
                        }
                    ]
                },
                output: {
                    '😇': 0,
                    '😀': 0,
                    '😟': 1,
                    '😰': 0,
                }
            },
            {
                input: {
                    now: Date.now(),
                    budget: {
                        initialAmount: 5000,
                        leftAmount: -1000,
                        spentAmount: 6000,
                    },
                    project: {
                        archived: false,
                        endAt: {
                            date: "2022-03-16 22:01:06.000000",
                            timezone: "UTC",
                            timezone_type: 3,
                        },
                        startAt: {
                            date: "2021-05-16 22:01:06.000000",
                            timezone: "UTC",
                            timezone_type: 3,
                        },
                    },
                    risks: [
                        {
                            probability: "very_high",
                            resolvedAt: null,
                            identifiedAt: {
                                date: "2022-05-16 22:01:06.000000",
                                timezone: "UTC",
                                timezone_type: 3,
                            },
                            severity: "medium",
                        },
                        {
                            probability: "present",
                            resolvedAt: null,
                            identifiedAt: {
                                date: "2022-05-18 22:01:06.000000",
                                timezone: "UTC",
                                timezone_type: 3,
                            },
                            severity: "breaking",
                        }
                    ]
                },
                output: {
                    '😇': 0,
                    '😀': 0,
                    '😟': 0,
                    '😰': 1,
                }
            },
        ];

        const net = new brain.NeuralNetwork(config);

        net.train(trainingData);

        const output = net.run(projectData);
        console.log(output);
    }
}
