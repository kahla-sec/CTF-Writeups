from django.http import HttpResponse,Http404,HttpResponseRedirect
from django.shortcuts import render,redirect
import pickle
import base64
from .forms import Myform,UploadForm
def home(request):
    try:
        upload=UploadForm()
        if request.method=='POST' :
            form=Myform(request.POST)
            if form.is_valid():
                request.session['shinobi']={
                'Name':form.cleaned_data['Name'],
                'Age':form.cleaned_data['Age'],
                'Rank':form.cleaned_data['Rank']}
                return render(request,"home.html",{'form':form.as_p,'dict':request.session['shinobi'].items(),'upload':upload.as_p})
            else:
                return render(request,"home.html",{'form':form.as_p,'dict':request.session['shinobi'].items(),'upload':upload.as_p})

        else :
            if 'message' in request.session:
                msg=request.session['message']
            else:
                msg=""
            if 'shinobi' not in request.session:
                request.session['shinobi']={}
            form=Myform()
            return render(request,"home.html",{'form':form.as_p,'msg':msg,'upload':upload.as_p,'dict':request.session['shinobi'].items()})
    except Exception:
        return HttpResponse('No No dude ; I\'ve encountered an error')
def download(request):
    try:
        if 'shinobi' not in request.session:
            request.session['shinobi']=[]
            return redirect("/")
        pick=pickle.dumps(request.session['shinobi'])
        data=base64.b64encode(pick).decode()
        response=HttpResponse(data,content_type='application/force-download')
        response['Content-Disposition']='attachment; filename="pickle_shinobi.data"'
        return response
    except Exception:
        return HttpResponse('i\'ve Encountered an error sorry')
def upload(request):
    try:
        if request.method=='POST':
            up=UploadForm(request.POST,request.FILES)
            if up.is_valid():
                raw_data=request.FILES['file'].read()
                request.session['shinobi']=pickle.loads(base64.b64decode(raw_data.decode()))
                request.session['message']="Uploaded Successfully"
                return HttpResponseRedirect("/")
    except Exception:
        return HttpResponse('I\'ve encountered an error sorry')




            



