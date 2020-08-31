import PageStyleEdit from "./components/PageStyleEdit";

let instace = new PageStyleEdit();
instace.form = $('form.read_style');
instace.resetButton = instace.form.find('.reset').first();
instace.init();