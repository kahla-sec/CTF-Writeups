from django import forms 

class Myform(forms.Form):
    liste= (
    ('Genin', 'Genin'),
    ('Chonin', 'Chonin'),
    ('Jonin', 'Jonin'),
    ('Hockage', 'Hockage'),
    )
    Name=forms.CharField(max_length=100,widget=forms.TextInput(attrs={'class' : 'input--style-2'}))
    Age=forms.IntegerField(max_value=40,min_value=11,widget=forms.NumberInput(attrs={'class' : 'input--style-2'}))
    Rank=forms.ChoiceField(choices=liste,widget=forms.Select(attrs={'class' : "rs-select2 js-select-simple select--no-search"}))
class UploadForm(forms.Form):
    file=forms.FileField(max_length=40)