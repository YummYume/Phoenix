const project = document.getElementById('project-data');
const projectForecast = document.getElementById('project-forecast');
const ai = document.getElementById('ai-data');

if (project && projectForecast && ai) {
    const projectData = JSON.parse(project.dataset.project);
    const aiData = JSON.parse(JSON.parse(ai.dataset.ai));
    // data that the AI receives
    console.log(projectData);

    const net = new brain.NeuralNetwork();
    net.fromJSON(aiData);

    const output = net.run(projectData);
    console.log(output);

    let sortable = [];
    for (let result in output) {
        sortable.push([result, output[result]]);
    }

    sortable.sort((prev, current) => current[1] - prev[1]);

    const forecast = sortable[0][0];

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
